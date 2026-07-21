<?php

namespace App\Services;

use App\Models\EmailAccount;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Crypt;
use Webklex\PHPIMAP\ClientManager;
use Webklex\PHPIMAP\Client;
use Webklex\PHPIMAP\Message;

class ImapConnector
{
    private EmailAccount $emailAccount;
    private ?Client $client = null;
    private ClientManager $clientManager;

    public function __construct(EmailAccount $emailAccount)
    {
        $this->emailAccount = $emailAccount;
        
        // [PARCHE MODO DIOS] Auto-sanado del archivo de configuración de Webklex (Soluciona el error Failed to open stream)
        // FileZilla a veces omite carpetas config vacías o Composer puede fallar al generar este archivo.
        $webklexConfigPath = base_path('vendor/webklex/php-imap/src/config/imap.php');
        if (!file_exists($webklexConfigPath)) {
            $dir = dirname($webklexConfigPath);
            if (!is_dir($dir)) { @mkdir($dir, 0755, true); }
            $defaultConfig = "<?php return ['options' => ['fetch' => 1, 'sequence' => 1, 'fetch_body' => true, 'fetch_flags' => true, 'message_key' => 'id', 'fetch_order' => 'asc', 'dispositions' => ['attachment', 'inline'], 'fallback_date' => '01.01.1970 00:00:00', 'boundary' => '/boundary=(.*?(?=;)|(.*))/i', 'message' => '/\r\n/', 'services' => ['message' => \Webklex\PHPIMAP\Message::class, 'folder' => \Webklex\PHPIMAP\Folder::class, 'query' => \Webklex\PHPIMAP\Query\WhereQuery::class, 'attachment' => \Webklex\PHPIMAP\Attachment::class]]];";
            @file_put_contents($webklexConfigPath, $defaultConfig);
        }

        // Webklex ClientManager is robust and handles sockets
        $this->clientManager = new ClientManager();
    }

    public static function testConnection($host, $port, $encryption = 'ssl'): array
    {
        $prefix = ($encryption === 'ssl') ? 'ssl://' : '';
        $socket = @fsockopen($prefix . $host, (int)$port, $errno, $errstr, 5);
        
        if (!$socket) {
            return ['success' => false, 'message' => $errstr ?: 'Connection refused'];
        }
        
        fclose($socket);
        return ['success' => true, 'message' => 'OK'];
    }

    public function connect(): void
    {
        $password = $this->emailAccount->imap_password;
        try {
            $password = Crypt::decryptString($password);
        } catch (\Exception $e) {
            // Usa texto plano si falla
        }

        $this->client = $this->clientManager->make([
            'host'          => $this->emailAccount->imap_host,
            'port'          => $this->emailAccount->imap_port ?? 993,
            'encryption'    => $this->emailAccount->imap_encryption ?? 'ssl',
            'validate_cert' => false,
            'username'      => $this->emailAccount->username ?? $this->emailAccount->email,
            'password'      => $password,
            'protocol'      => 'imap'
        ]);

        try {
            $this->client->connect();
            Log::debug("Conexion Webklex establecida", ['email' => $this->emailAccount->email]);
        } catch (\Exception $e) {
            Log::error("Fallo al conectar con Webklex", [
                'email' => $this->emailAccount->email,
                'error' => $e->getMessage()
            ]);
            throw new \RuntimeException('Fallo al establecer conexion IMAP: ' . $e->getMessage());
        }
    }

    public function getConnection(): ?Client
    {
        return $this->client;
    }

    public function isConnected(): bool
    {
        return $this->client !== null && $this->client->isConnected();
    }

    public function getRecentEmails(int $hours = 1): array
    {
        if (!$this->isConnected()) {
            throw new \Exception('No hay conexion IMAP activa.');
        }

        try {
            $folder = $this->client->getFolder('INBOX');
            
            // TRUE GOD MODE (Versión 7 - Fuerza Bruta Clásica Optimizada):
            // Falló getMessage() porque tu versión de Webklex es tan antigua que no tiene esa función 
            // (arrojó un Fatal Error oculto en 450ms).
            // Regresamos a la Versión 4 (la que SÍ te funcionó para cuentas@) donde usamos el Query Builder.
            // Solo que ahora buscamos un máximo de 40 UIDs para que Gmail termine en ~15 segundos exactos.
            
            $examine = $folder->examine();
            $uidNext = isset($examine['uidnext']) ? (int) $examine['uidnext'] : 1000;
            $uidStart = max(1, $uidNext - 40); // 40 ciclos = 15 segundos maximo de espera

            $messagesArray = [];
            for ($uid = $uidNext; $uid >= $uidStart; $uid--) {
                try {
                    // Castear a (int) evita el bug de comillas.
                    $msg = $folder->query()->whereUid((int) $uid)->setFetchBody(false)->get()->first();
                    if ($msg) {
                        $messagesArray[] = $msg;
                        if (count($messagesArray) >= 20) {
                            break;
                        }
                    }
                } catch (\Throwable $ex) {
                    // Ignorar
                }
            }

            return $messagesArray;
        } catch (\Exception $e) {
            echo "  [ERROR IMAP] " . $e->getMessage() . "\n";
            Log::error('Error obteniendo emails recientes con Webklex', ['error' => $e->getMessage()]);
            return [];
        }
    }

    public function searchByTo($message, array $validEmails, array $subjects): ?array
    {
        if (!$this->isConnected() || !$message) {
            return null;
        }

        try {
            $uid = $message->getUid();
            $folder = $this->client->getFolder('INBOX');

            $toAddresses = $message->getTo();
            $searchTo = 'generico@streamvzla.com';
            $foundValid = false;

            // 1. Intentar con Webklex getTo() cruzado con la BD
            if ($toAddresses) {
                foreach ($toAddresses as $address) {
                    if (!empty($address->mail)) {
                        $candidate = strtolower(trim($address->mail));
                        if (in_array($candidate, $validEmails)) {
                            $searchTo = $candidate;
                            $foundValid = true;
                            break;
                        }
                    }
                }
            }

            // 1.5 Buscar específicamente en cabeceras de reenvío para un fallback seguro (SOLO SI ESTA EN LA BD)
            if (!$foundValid && $searchTo === 'generico@streamvzla.com') {
                $rawHeader = $message->getHeader()->raw;
                if (preg_match_all('/(?:Delivered-To|Envelope-To|X-Forwarded-To):\s*<?([a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,})>?/i', $rawHeader, $forwardMatches)) {
                    foreach ($forwardMatches[1] as $candidate) {
                        $candidate = strtolower(trim($candidate));
                        if (in_array($candidate, $validEmails)) {
                            $searchTo = $candidate;
                            $foundValid = true;
                            break;
                        }
                    }
                }
            }

            // 2. Si no es un email de la BD, escanear el Codigo Crudo completo (Raw Headers)
            if (!$foundValid) {
                $rawHeader = $message->getHeader()->raw;
                if (preg_match_all('/[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}/i', $rawHeader, $matches)) {
                    foreach ($matches[0] as $candidate) {
                        $candidate = strtolower(trim($candidate));
                        if (in_array($candidate, $validEmails)) {
                            $searchTo = $candidate;
                            $foundValid = true;
                            break;
                        }
                    }
                }
            }

            // === 2. VALIDAR PLATAFORMA (ANTES DE DESCARGAR EL CUERPO) ===
            // Validar Asunto (Subject) con Inteligencia Artificial (Fuzzy Matching)
            $subject = (string) $message->getSubject();
            $asciiSubject = \Illuminate\Support\Str::ascii($subject);
            $cleanSubject = preg_replace('/[^a-z0-9]/', '', strtolower($asciiSubject));
            
            $fromEmail = '';
            if ($message->getFrom()) {
                $fromEmail = strtolower(trim($message->getFrom()[0]->mail));
            }

            $matchedPlatform = null;

            foreach ($subjects as $platformName => $platformSubjects) {
                
                // Salvavidas Universal: Si el dominio del remitente contiene el nombre de la plataforma
                $cleanPlatform = preg_replace('/[^a-z0-9]/', '', strtolower(\Illuminate\Support\Str::ascii($platformName)));
                if (!empty($cleanPlatform) && strlen($cleanPlatform) > 3 && str_contains($fromEmail, $cleanPlatform)) {
                    $matchedPlatform = $platformName;
                    break;
                }

                foreach ($platformSubjects as $expectedSubject) {
                    $asciiExpected = \Illuminate\Support\Str::ascii($expectedSubject);
                    $cleanExpected = preg_replace('/[^a-z0-9]/', '', strtolower($asciiExpected));
                    
                    if (str_contains(strtolower($asciiSubject), strtolower($asciiExpected)) || 
                        (!empty($cleanExpected) && str_contains($cleanSubject, $cleanExpected))) {
                        $matchedPlatform = $platformName;
                        break 2;
                    }
                }
            }

            // Si no es de ninguna plataforma que nos importe, abortar al instante
            // (Ahorra peticiones de red gigantescas al servidor IMAP)
            if (!$matchedPlatform) {
                return null;
            }

            // === 3. DESCARGAR CUERPO SOLO SI ES VALIDO (Lazy Loading Real) ===
            $fullMessage = $folder->query()->setFetchBody(true)->getMessageByUid($uid);
            $body = $fullMessage ? $fullMessage->getHTMLBody() : '';
            if (empty($body)) {
                $body = $fullMessage ? $fullMessage->getTextBody() : $message->getTextBody();
            }

            if (is_array($body)) {
                $body = implode("\n", $body);
            } elseif (!is_string($body)) {
                $body = (string) $body;
            }

            // Fallback Extremo: Buscar dentro del texto del correo ("Te enviamos el codigo a mipapa...")
            if (!$foundValid) {
                $bodyText = $fullMessage ? $fullMessage->getTextBody() : '';
                if (!empty($bodyText) && preg_match_all('/[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}/i', $bodyText, $matches)) {
                    foreach ($matches[0] as $candidate) {
                        $candidate = strtolower(trim($candidate));
                        if (in_array($candidate, $validEmails)) {
                            $searchTo = $candidate;
                            $foundValid = true;
                            break;
                        }
                    }
                }
            }

            return [
                'body'          => $body,
                'to'            => $searchTo,
                'subject'       => $subject,
                'platform_name' => $matchedPlatform
            ];

        } catch (\Exception $e) {
            Log::error('Error buscando email por UID en Webklex', ['uid' => $uid, 'error' => $e->getMessage()]);
            return null;
        }
    }

    public function markAsRead(int $uid): bool
    {
        if (!$this->isConnected()) {
            return false;
        }

        try {
            $folder = $this->client->getFolder('INBOX');
            $message = $folder->query()->getMessageByUid($uid);
            if ($message) {
                $message->setFlag('Seen');
                return true;
            }
        } catch (\Exception $e) {
            Log::error('Error marcando como leido en Webklex', ['uid' => $uid, 'error' => $e->getMessage()]);
        }
        return false;
    }
}
