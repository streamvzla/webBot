<?php

namespace App\Services;

use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Log;
use App\Models\EmailAccount;

/**
 * ImapConnector - Manejo de conexiones IMAP seguras
 *
 * ⚠️ ADVERTENCIAS TÉCNICAS:
 * - Nunca usar IMAP en modo "catch-all" sin autenticación por cuenta específica
 * - Implementar rate limiting a nivel aplicación Y servidor (fail2ban)
 * - Regex de extracción deben actualizarse periódicamente (servicios cambian templates)
 * - Auditoría mensual de logs de acceso a BD para imap_passwords
 */
class ImapConnector
{
    protected ?EmailAccount $emailAccount = null;
    protected $connection = null;
    protected int $timeout;

    public function __construct(?EmailAccount $emailAccount = null, int $timeout = 30)
    {
        $this->emailAccount = $emailAccount;
        $this->timeout = $timeout;
    }

    /**
     * Obtener la conexión IMAP actual
     */
    public function getConnection()
    {
        return $this->connection;
    }

    /**
     * Obtener flags SSL apropiados según el servidor
     * Gmail requiere validación de certificado, servidores privados pueden usar novalidate-cert
     */
    protected function getSslFlags(string $host): string
    {
        // Detectar Gmail - requiere validación de certificado
        if (strpos($host, 'gmail.com') !== false ||
            strpos($host, 'googlemail.com') !== false) {
            Log::info('Servidor Gmail detectado, usando validación de certificado');
            return '/imap/ssl/validate-cert';
        }

        // Servidores privados pueden usar novalidate-cert
        return '/imap/ssl/novalidate-cert';
    }

    /**
     * Conectar al servidor IMAP
     *
     * @throws \Exception
     */
    public function connect(): self
    {
        if (!$this->emailAccount) {
            throw new \Exception('No hay cuenta de correo configurada.');
        }

        // Limpiar errores IMAP previos
        imap_errors();

        // Desencriptar contraseña (si está encriptada)
        $password = $this->emailAccount->imap_password;
        try {
            $password = Crypt::decryptString($this->emailAccount->imap_password);
        } catch (\Exception $e) {
            // La contraseña no está encriptada, usar tal cual
            Log::info('Contraseña IMAP no está encriptada, usando texto plano', [
                'email' => $this->emailAccount->email,
            ]);
            $password = $this->emailAccount->imap_password;
        }

        // Construir string de conexión IMAP
        $host = $this->emailAccount->imap_host;
        $port = $this->emailAccount->imap_port ?? 993;
        $encryption = $this->emailAccount->imap_encryption ?? 'ssl';

        $flags = match ($encryption) {
            'ssl' => $this->getSslFlags($host),
            'tls' => '/imap/tls/validate-cert',
            default => $this->getSslFlags($host),
        };

        $mailbox = "{{$host}:{$port}{$flags}}";

        Log::info('Intentando conectar a servidor IMAP', [
            'host' => $host,
            'port' => $port,
            'encryption' => $encryption,
            'flags' => $flags,
            'mailbox' => $mailbox,
            'email' => $this->emailAccount->email,
            'username' => $this->emailAccount->username,
        ]);

        // Verificar si el puerto es accesible primero
        $socketTest = ImapConnector::testConnection($host, $port, $encryption);
        Log::info('Prueba de puerto IMAP', $socketTest);

        if (!$socketTest['success']) {
            $error = $socketTest['message'];
            Log::error('Puerto IMAP no accesible', [
                'host' => $host,
                'port' => $port,
                'error' => $error,
            ]);
            throw new \Exception("No se puede acceder al puerto IMAP {$port}: {$error}");
        }

        // Establecer timeout para la conexión
        imap_timeout(IMAP_OPENTIMEOUT, $this->timeout);
        imap_timeout(IMAP_READTIMEOUT, $this->timeout);
        imap_timeout(IMAP_WRITETIMEOUT, $this->timeout);
        imap_timeout(IMAP_CLOSETIMEOUT, $this->timeout);

        Log::info('Timeouts IMAP configurados', ['timeout' => $this->timeout]);

        // Abrir conexión
        $this->connection = @imap_open(
            $mailbox,
            $this->emailAccount->email,
            $password,
            OP_READONLY,
            1, // Intentos
            [
                'DISABLE_AUTHENTICATOR' => ['GSSAPI', 'NTLM'],
            ]
        );

        if (!$this->connection) {
            $error = imap_last_error();
            $allErrors = imap_errors();

            Log::error('Error conectando a IMAP - Detalles completos', [
                'host' => $host,
                'port' => $port,
                'email' => $this->emailAccount->email,
                'username' => $this->emailAccount->username,
                'error' => $error,
                'all_errors' => $allErrors,
                'last_error' => imap_last_error(),
            ]);

            throw new \Exception("Error de conexión IMAP: {$error}");
        }

        // Verificar que la conexión esté activa
        if (!imap_ping($this->connection)) {
            Log::warning('Conexión IMAP establecida pero no responde a ping', [
                'email' => $this->emailAccount->email,
            ]);
        }

        // Obtener información del buzón
        $mailboxInfo = imap_mailboxmsginfo($this->connection);
        Log::info('Conexión IMAP exitosa', [
            'email' => $this->emailAccount->email,
            'messages_count' => $mailboxInfo->Nmsgs ?? 0,
            'recent_count' => $mailboxInfo->Recent ?? 0,
        ]);

        return $this;
    }

    /**
     * Buscar emails por asunto
     */
    public function searchBySubject(string $subject): array
    {
        if (!$this->connection) {
            throw new \Exception('No hay conexión IMAP activa.');
        }

        // Sanitizar asunto para búsqueda
        $searchSubject = imap_utf8($subject);

        // Buscar emails con el asunto específico
        $emails = imap_search($this->connection, 'SUBJECT "' . addslashes($searchSubject) . '"', SE_UID);

        if (!$emails) {
            return [];
        }

        return $emails;
    }

    /**
     * Obtener el cuerpo del email (solo texto plano, sin HTML)
     */
    public function getEmailBody(int $msgNo): string
    {
        if (!$this->connection) {
            throw new \Exception('No hay conexión IMAP activa.');
        }

        // Obtener estructura del email
        $structure = imap_fetchstructure($this->connection, $msgNo, FT_UID);

        if (!$structure) {
            return '';
        }

        // Extraer cuerpo del email (versión texto plano)
        $body = $this->extractBodyPart($this->connection, $msgNo, $structure, false);

        return $body ?: '';
    }

    /**
     * Obtener el cuerpo del email con formato HTML
     */
    public function getEmailHtmlBody(int $msgNo): string
    {
        if (!$this->connection) {
            throw new \Exception('No hay conexión IMAP activa.');
        }

        // Obtener estructura del email
        $structure = imap_fetchstructure($this->connection, $msgNo, FT_UID);

        if (!$structure) {
            return '';
        }

        // Extraer cuerpo del email (versión HTML sin strip_tags)
        $body = $this->extractBodyPart($this->connection, $msgNo, $structure, true);

        return $body ?: '';
    }

    /**
     * Obtener datos completos del email (cuerpo + fecha)
     */
    public function getEmailData(int $msgNo, bool $asHtml = false): array
    {
        if (!$this->connection) {
            throw new \Exception('No hay conexión IMAP activa.');
        }

        // Obtener estructura para determinar si es HTML
        $structure = imap_fetchstructure($this->connection, $msgNo, FT_UID);
        $isHtml = $this->isHtmlEmail($structure);

        // Si se pide HTML pero el email no tiene HTML, forzar texto plano
        $useHtml = $asHtml && $isHtml;

        // Obtener cuerpo
        $body = $this->extractBodyPart($this->connection, $msgNo, $structure, $useHtml);

        // Obtener fecha del email
        $overview = imap_fetch_overview($this->connection, $msgNo, FT_UID);
        $date = $overview[0]->date ?? null;
        $receivedDate = null;

        if ($date) {
            try {
                $receivedDate = \Carbon\Carbon::parse($date)->format('d/m/Y H:i:s');
            } catch (\Exception $e) {
                $receivedDate = 'Fecha no disponible';
            }
        }

        return [
            'body' => $body,
            'received_at' => $receivedDate,
            'uid' => $msgNo,
            'is_html' => $isHtml,
        ];
    }

    /**
     * Verificar si el email tiene contenido HTML
     */
    protected function isHtmlEmail($structure): bool
    {
        if (!$structure) return false;

        // Si el mensaje principal es HTML
        if (isset($structure->subtype) && strtoupper($structure->subtype) === 'HTML') {
            return true;
        }

        // Si es multipart, buscar partes HTML
        if (isset($structure->parts) && count($structure->parts) > 0) {
            foreach ($structure->parts as $part) {
                if (isset($part->subtype) && strtoupper($part->subtype) === 'HTML') {
                    return true;
                }
                // Recursivamente buscar en partes anidadas
                if ($part->type == 1 && isset($part->parts)) {
                    if ($this->isHtmlEmail($part)) {
                        return true;
                    }
                }
            }
        }

        return false;
    }

    /**
     * Extraer la parte correcta del cuerpo del email
     */
    protected function extractBodyPart($connection, $msgNo, $structure, bool $asHtml): string
    {
        // Si no tiene partes, es un mensaje simple
        if (!isset($structure->parts) || count($structure->parts) == 0) {
            $body = imap_body($connection, $msgNo, FT_UID);
            return $this->decodeBody($body, $structure->encoding ?? 0);
        }

        // Buscar la parte correcta (text/plain o text/html)
        return $this->findAndExtractPart($connection, $msgNo, $structure, $asHtml, '');
    }

    /**
     * Buscar recursivamente la parte correcta del email
     */
    protected function findAndExtractPart($connection, $msgNo, $structure, bool $asHtml, string $prefix): string
    {
        $parts = $structure->parts;

        foreach ($parts as $idx => $part) {
            $partNum = $prefix ? $prefix . '.' . ($idx + 1) : ($idx + 1);

            // Si es multipart anidado, buscar dentro
            if ($part->type == 1 && isset($part->parts) && count($part->parts) > 0) {
                $result = $this->findAndExtractPart($connection, $msgNo, $part, $asHtml, $partNum);
                if (!empty($result)) {
                    return $result;
                }
            }

            // Verificar si es text/plain o text/html
            if ($part->type == 0) { // TEXT type
                $subtype = strtoupper($part->subtype ?? 'PLAIN');

                if ($subtype === 'HTML' && $asHtml) {
                    // Extraer parte HTML
                    $body = imap_fetchbody($connection, $msgNo, $partNum, FT_UID);
                    return $this->decodeBody($body, $part->encoding ?? 0);
                }

                if ($subtype === 'PLAIN' && !$asHtml) {
                    // Extraer parte texto plano
                    $body = imap_fetchbody($connection, $msgNo, $partNum, FT_UID);
                    return $this->decodeBody($body, $part->encoding ?? 0);
                }
            }
        }

        // Si no encontramos la parte exacta, buscar fallback
        foreach ($parts as $idx => $part) {
            $partNum = $prefix ? $prefix . '.' . ($idx + 1) : ($idx + 1);

            if ($part->type == 0) { // TEXT type
                $subtype = strtoupper($part->subtype ?? 'PLAIN');
                $body = imap_fetchbody($connection, $msgNo, $partNum, FT_UID);
                $decodedBody = $this->decodeBody($body, $part->encoding ?? 0);

                // Si queremos HTML pero solo hay texto, convertir a HTML básico
                if ($asHtml && $subtype === 'PLAIN') {
                    return nl2br(htmlspecialchars($decodedBody));
                }

                // Si queremos texto pero hay HTML, strip tags
                if (!$asHtml && $subtype === 'HTML') {
                    return strip_tags($decodedBody);
                }

                return $decodedBody;
            }

            // Buscar en partes anidadas
            if ($part->type == 1 && isset($part->parts) && count($part->parts) > 0) {
                $result = $this->findAndExtractPart($connection, $msgNo, $part, $asHtml, $partNum);
                if (!empty($result)) {
                    return $result;
                }
            }
        }

        return '';
    }

    /**
     * Decodificar cuerpo según el encoding
     */
    protected function decodeBody(string $body, int $encoding): string
    {
        if (empty($body)) {
            return '';
        }

        $decoded = '';
        switch ($encoding) {
            case 3: // BASE64
                $decoded = imap_base64($body);
                break;
            case 4: // QUOTED-PRINTABLE
                $decoded = imap_qprint($body);
                break;
            default:
                $decoded = $body;
        }

        // Detectar y convertir codificación a UTF-8
        return $this->ensureUtf8($decoded);
    }

    /**
     * Asegurar que el texto esté en UTF-8
     */
    protected function ensureUtf8(string $text): string
    {
        if (empty($text)) {
            return '';
        }

        // Verificar si ya es UTF-8 válido
        if (mb_check_encoding($text, 'UTF-8')) {
            return $text;
        }

        // Intentar detectar la codificación original
        $detectedEncoding = mb_detect_encoding($text, ['ISO-8859-1', 'Windows-1252', 'ISO-8859-15', 'UTF-8'], true);

        if ($detectedEncoding && $detectedEncoding !== 'UTF-8') {
            // Convertir a UTF-8
            $converted = mb_convert_encoding($text, 'UTF-8', $detectedEncoding);
            if ($converted !== false) {
                return $converted;
            }
        }

        // Si falla la detección, forzar conversión desde ISO-8859-1 (común en emails antiguos)
        $converted = mb_convert_encoding($text, 'UTF-8', 'ISO-8859-1');
        if ($converted !== false) {
            return $converted;
        }

        // Último recurso: ignorar errores de conversión
        return mb_convert_encoding($text, 'UTF-8', 'ISO-8859-1');
    }

    /**
     * Buscar emails por asunto Y destinatario (TO)
     * Este método filtra emails que estén dirigidos a un email específico
     * Útil para cuentas catch-all que reciben emails de múltiples destinatarios
     */
    public function searchBySubjectAndTo(string $subject, string $toEmail): array
    {
        if (!$this->connection) {
            throw new \Exception('No hay conexión IMAP activa.');
        }

        // Sanitizar para búsqueda IMAP
        $searchSubject = imap_utf8($subject);
        $searchTo = strtolower(trim($toEmail));

        // Buscar emails con el asunto específico
        $emailsBySubject = imap_search($this->connection, 'SUBJECT "' . addslashes($searchSubject) . '"', SE_UID);

        if (!$emailsBySubject) {
            return [];
        }

        // Filtrar por destinatario (TO)
        $filteredEmails = [];
        foreach ($emailsBySubject as $uid) {
            $overview = @imap_fetch_overview($this->connection, $uid, FT_UID);
            if ($overview && isset($overview[0]->to)) {
                $toAddress = $this->extractEmailAddress($overview[0]->to);
                if ($toAddress && strtolower($toAddress) === $searchTo) {
                    $filteredEmails[] = $uid;
                }
            }
        }

        return $filteredEmails;
    }

    /**
     * Extraer dirección de email de una cadena TO de IMAP
     * Método público para uso externo
     */
    public function extractEmailAddress(string $toHeader): ?string
    {
        // Usar regex para extraer solo direcciones de email válidas
        // Esto maneja casos corruptos como "email@/ruta/absoluta"
        if (preg_match('/([a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,})/', $toHeader, $matches)) {
            return $matches[1];
        }

        // Formato estándar: "Nombre" <email@dominio.com>
        if (preg_match('/<([^>]+)>/', $toHeader, $matches)) {
            $email = $matches[1];
            if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
                return $email;
            }
        }

        // Email directo sin formato
        if (filter_var($toHeader, FILTER_VALIDATE_EMAIL)) {
            return $toHeader;
        }

        return null;
    }

    /**
     * Buscar emails solo por destinatario (TO)
     * Sin filtro de subject
     */
    public function searchByTo(string $toEmail): array
    {
        if (!$this->connection) {
            throw new \Exception('No hay conexión IMAP activa.');
        }

        $searchTo = strtolower(trim($toEmail));

        // Obtener emails recientes (últimas 24 horas para ser amplios)
        $since = date('d-M-Y', strtotime('-24 hours'));
        $recentEmails = imap_search($this->connection, "SINCE {$since}", SE_UID);

        if (!$recentEmails) {
            return [];
        }

        // Filtrar por destinatario
        $filteredEmails = [];
        foreach ($recentEmails as $uid) {
            $overview = @imap_fetch_overview($this->connection, $uid, FT_UID);
            if ($overview && isset($overview[0]->to)) {
                $toAddress = $this->extractEmailAddress($overview[0]->to);
                if ($toAddress && strtolower($toAddress) === $searchTo) {
                    $filteredEmails[] = $uid;
                }
            }
        }

        return $filteredEmails;
    }

    /**
     * Eliminar acentos de una cadena (maneja UTF-8 correctamente)
     */
    protected function removeAccents(string $string): string
    {
        if (empty($string)) {
            return $string;
        }

        // Normalizar a Form D (descomposición canónica)
        if (function_exists('normalizer_normalize')) {
            $normalized = normalizer_normalize($string, \Normalizer::FORM_D);
            if ($normalized !== false) {
                $string = $normalized;
            }
        }

        // Eliminar marcas de combinación (diacríticos como acentos)
        $string = preg_replace('/\p{M}/u', '', $string);

        // Convertir caracteres extendidos latinos a ASCII equivalente
        $replacements = [
            'à' => 'a', 'á' => 'a', 'ä' => 'a', 'â' => 'a', 'ã' => 'a', 'ā' => 'a', 'ą' => 'a', 'å' => 'a',
            'è' => 'e', 'é' => 'e', 'ë' => 'e', 'ê' => 'e', 'ę' => 'e', 'ē' => 'e', 'ė' => 'e',
            'ì' => 'i', 'í' => 'i', 'ï' => 'i', 'î' => 'i', 'į' => 'i', 'ī' => 'i',
            'ò' => 'o', 'ó' => 'o', 'ö' => 'o', 'ô' => 'o', 'õ' => 'o', 'ø' => 'o', 'ō' => 'o', 'ő' => 'o',
            'ù' => 'u', 'ú' => 'u', 'ü' => 'u', 'û' => 'u', 'ų' => 'u', 'ū' => 'u', 'ű' => 'u',
            'ñ' => 'n', 'ń' => 'n',
            'ç' => 'c', 'ć' => 'c', 'č' => 'c',
            'ż' => 'z', 'ź' => 'z', 'ž' => 'z',
            'ś' => 's', 'š' => 's', 'ș' => 's',
            'ł' => 'l', 'đ' => 'd',
            'ß' => 'ss',
            'œ' => 'oe', 'æ' => 'ae',
            'À' => 'A', 'Á' => 'A', 'Ä' => 'A', 'Â' => 'A', 'Ã' => 'A', 'Å' => 'A', 'Ā' => 'A', 'Ą' => 'A',
            'È' => 'E', 'É' => 'E', 'Ë' => 'E', 'Ê' => 'E', 'Ę' => 'E', 'Ē' => 'E', 'Ė' => 'E',
            'Ì' => 'I', 'Í' => 'I', 'Ï' => 'I', 'Î' => 'I', 'Į' => 'I', 'Ī' => 'I',
            'Ò' => 'O', 'Ó' => 'O', 'Ö' => 'O', 'Ô' => 'O', 'Õ' => 'O', 'Ø' => 'O', 'Ō' => 'O', 'Ő' => 'O',
            'Ù' => 'U', 'Ú' => 'U', 'Ü' => 'U', 'Û' => 'U', 'Ū' => 'U', 'Ű' => 'U', 'Ų' => 'U',
            'Ñ' => 'N', 'Ń' => 'N',
            'Ç' => 'C', 'Ć' => 'C', 'Č' => 'C',
            'Ż' => 'Z', 'Ź' => 'Z', 'Ž' => 'Z',
            'Ś' => 'S', 'Š' => 'S', 'Ș' => 'S',
            'Ł' => 'L', 'Đ' => 'D',
        ];

        return strtolower(strtr($string, $replacements));
    }

    /**
     * Buscar emails por destinatario Y verificar que coincida con subject
     * Retorna el email más reciente que cumpla ambos criterios
     */
    public function findLatestEmailByRecipientAndPlatform(string $toEmail, array $subjects, int $hours = 72): ?array
    {
        if (!$this->connection) {
            throw new \Exception('No hay conexión IMAP activa.');
        }

        $searchTo = strtolower(trim($toEmail));

        // Obtener emails recientes
        $recentEmails = $this->getRecentEmails($hours);

        if (empty($recentEmails)) {
            Log::info('No se encontraron emails recientes para la búsqueda', [
                'recipient' => $searchTo,
                'hours' => $hours,
            ]);
            return null;
        }

        Log::info('Emails recientes encontrados', [
            'count' => count($recentEmails),
            'recipient' => $searchTo,
        ]);

        // Ordenar por UID (más reciente primero)
        rsort($recentEmails, SORT_NUMERIC);

        foreach ($recentEmails as $uid) {
            $overview = @imap_fetch_overview($this->connection, $uid, FT_UID);
            if (!$overview) continue;

            // Verificar destinatario
            if (!isset($overview[0]->to)) continue;
            $toAddress = $this->extractEmailAddress($overview[0]->to);
            if (!$toAddress || strtolower($toAddress) !== $searchTo) continue;

            Log::debug('Email coincide con destinatario', [
                'uid' => $uid,
                'to' => $toAddress,
            ]);

            // Verificar que coincida con algún subject de la plataforma
            $subject = isset($overview[0]->subject) ? imap_utf8($overview[0]->subject) : '';
            $subjectNormalized = strtolower($this->removeAccents($subject));

            foreach ($subjects as $platformSubject) {
                $patternNormalized = strtolower($this->removeAccents($platformSubject));
                if (stripos($subjectNormalized, $patternNormalized) !== false) {
                    Log::info('Email coincide con subject de plataforma', [
                        'uid' => $uid,
                        'subject' => $subject,
                        'matched_pattern' => $platformSubject,
                    ]);

                    // Email encontrado - obtener datos completos
                    $emailData = $this->getEmailData($uid, true);
                    $emailData['to'] = $toAddress;
                    $emailData['subject'] = $subject;
                    return $emailData;
                }
            }

            // Log cuando no coincide con ningún subject
            Log::debug('Email no coincide con ningún subject configurado', [
                'uid' => $uid,
                'subject' => $subject,
                'configured_subjects' => $subjects,
            ]);
        }

        Log::info('No se encontró email que cumpla todos los criterios', [
            'recipient' => $searchTo,
            'subjects' => $subjects,
            'emails_searched' => count($recentEmails),
        ]);

        return null;
    }

    /**
     * Obtener emails recientes (últimas N horas)
     */
    public function getRecentEmails(int $hours = 1): array
    {
        if (!$this->connection) {
            throw new \Exception('No hay conexión IMAP activa.');
        }

        // Limpiar errores previos
        imap_errors();

        $since = date('d-M-Y', strtotime("-{$hours} hours"));
        $emails = @imap_search($this->connection, "SINCE {$since}", SE_UID);

        if ($emails === false) {
            // Error en la búsqueda, obtener el error
            $error = imap_last_error();
            Log::warning('Error en búsqueda IMAP, reintentando...', ['error' => $error]);

            // Intentar una segunda vez
            $emails = @imap_search($this->connection, "SINCE {$since}", SE_UID);

            if ($emails === false) {
                Log::error('Error persistente en búsqueda IMAP', ['error' => imap_last_error()]);
                return [];
            }
        }

        return $emails ?: [];
    }

    /**
     * Cerrar conexión IMAP
     */
    public function disconnect(): void
    {
        if ($this->connection) {
            imap_close($this->connection, CL_EXPUNGE);
            $this->connection = null;
            Log::info('Conexión IMAP cerrada', [
                'email' => $this->emailAccount?->email,
            ]);
        }
    }

    public function __destruct()
    {
        // No llamar disconnect() aquí para evitar el error "Login aborted" durante el shutdown
        // La conexión se cerrará automáticamente cuando el script termine
    }

    /**
     * Verificar estado de conexión con detalles
     */
    public function getConnectionStatus(): array
    {
        $status = [
            'has_connection' => $this->connection !== null,
            'is_connected' => false,
            'last_error' => null,
            'server_info' => null,
            'mailbox_info' => null,
        ];

        if ($this->connection) {
            $status['is_connected'] = imap_ping($this->connection);
            $status['last_error'] = imap_last_error();

            // Obtener server info de forma segura
            $mailboxesResult = @imap_getmailboxes(
                $this->connection,
                "{{$this->emailAccount->imap_host}}",
                '*'
            );
            if ($mailboxesResult && is_array($mailboxesResult)) {
                $status['server_info'] = count($mailboxesResult) . ' mailboxes encontrados';
            } else {
                $status['server_info'] = 'No se pudo obtener lista de mailboxes';
            }

            // Obtener información del buzón
            $mailbox = @imap_mailboxmsginfo($this->connection);
            if ($mailbox) {
                $status['mailbox_info'] = [
                    'messages' => $mailbox->Nmsgs,
                    'recent' => $mailbox->Recent,
                    'unread' => $mailbox->Unread,
                    'deleted' => $mailbox->Deleted,
                    'size' => $mailbox->Size,
                ];
            }
        }

        return $status;
    }

    /**
     * Verificar si hay conexión activa
     */
    public function isConnected(): bool
    {
        return $this->connection !== null && imap_ping($this->connection);
    }

    /**
     * Obtener errores recientes de IMAP
     */
    public function getLastError(): string
    {
        return imap_last_error() ?: 'Sin errores';
    }

    /**
     * Obtener todos los errores de la conexión actual
     */
    public function getAllErrors(): array
    {
        $errors = [];
        $error = imap_errors();
        if ($error) {
            $errors = $error;
        }
        return $errors;
    }

    /**
     * Verificar si el servidor IMAP es alcanzable (sin autenticar)
     */
    public static function testConnection(string $host, int $port, string $encryption = 'ssl'): array
    {
        $result = [
            'success' => false,
            'host' => $host,
            'port' => $port,
            'encryption' => $encryption,
            'message' => '',
            'details' => null,
        ];

        $socket = @fsockopen(
            ($encryption === 'ssl' ? 'ssl://' : '') . $host,
            $port,
            $errno,
            $errstr,
            5
        );

        if ($socket) {
            $result['success'] = true;
            $result['message'] = 'Puerto IMAP accesible';
            fclose($socket);
        } else {
            $result['message'] = "No se puede conectar al puerto IMAP: {$errstr} ({$errno})";
        }

        return $result;
    }

    /**
     * Obtener bandejas disponibles
     */
    public function getMailboxes(): array
    {
        if (!$this->connection) {
            return [];
        }

        $mailboxes = [];
        $list = imap_getmailboxes($this->connection, "{{$this->emailAccount->imap_host}}", '*');

        if ($list && is_array($list)) {
            foreach ($list as $mailbox) {
                $mailboxes[] = [
                    'name' => is_string($mailbox->name) ? $mailbox->name : 'Unknown',
                    'attributes' => $mailbox->attributes ?? 0,
                ];
            }
        }

        return $mailboxes;
    }
}
