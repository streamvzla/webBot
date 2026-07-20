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
            
            // Usar since() con una ventana de 3 días para absorber cualquier diferencia horaria del servidor
            // y evitar extraer todo el historial de 19,000 correos (lo que causa timeout y retorna 0).
            $messages = $folder->query()
                ->unseen()
                ->since(now()->subDays(3))
                ->setFetchOrderDesc() // Más recientes primero
                ->limit(50)           // Máximo 50 para evitar colapsar la memoria y el tiempo
                ->setFetchBody(false)
                ->get();

            $messagesArray = $messages->all();

            // Tomar los últimos 50 (los más nuevos) y revertirlos
            if (count($messagesArray) > 50) {
                $messagesArray = array_slice($messagesArray, -50);
            }
            return array_reverse($messagesArray);
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
                        // Guardar por si acaso ninguno sirve
                        if ($searchTo === 'generico@streamvzla.com') {
                            $searchTo = $candidate;
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
                        // Si no es valido pero seguimos en generico, guardarlo por si acaso
                        // Ignorar correos obvios del remitente
                        if ($searchTo === 'generico@streamvzla.com' && !str_contains($candidate, 'netflix') && !str_contains($candidate, 'disney') && !str_contains($candidate, 'max') && !str_contains($candidate, 'amazon') && !str_contains($candidate, 'google')) {
                            $searchTo = $candidate;
                        }
                    }
                }
            }

            // 3. Fallback Extremo: Buscar dentro del texto del correo ("Te enviamos el codigo a mipapa...")
            if (!$foundValid) {
                $bodyText = $message->getTextBody() ?? '';
                if (preg_match_all('/[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}/i', $bodyText, $matches)) {
                    foreach ($matches[0] as $candidate) {
                        $candidate = strtolower(trim($candidate));
                        if (in_array($candidate, $validEmails)) {
                            $searchTo = $candidate;
                            $foundValid = true;
                            break;
                        }
                        // Si no es valido pero seguimos en generico, guardarlo por si acaso
                        if ($searchTo === 'generico@streamvzla.com' && !str_contains($candidate, 'netflix') && !str_contains($candidate, 'disney') && !str_contains($candidate, 'max') && !str_contains($candidate, 'amazon') && !str_contains($candidate, 'google')) {
                            $searchTo = $candidate;
                        }
                    }
                }
            }

            // Descargar el cuerpo del correo de forma segura (Lazy Loading)
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

            if ($matchedPlatform) {
                return [
                    'body'          => $body,
                    'to'            => $searchTo,
                    'subject'       => $subject,
                    'platform_name' => $matchedPlatform
                ];
            }

            return null;

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
