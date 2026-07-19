<?php

namespace App\Services;

use App\Models\PlatformSubject;
use Illuminate\Support\Facades\Log;

class ImapService
{
    private ?object $connection = null;
    private string $host;
    private int $port;
    private string $encryption;
    private string $username;
    private string $password;

    public function __construct(
        string $host,
        int $port,
        string $encryption,
        string $username,
        string $password
    ) {
        $this->host = $host;
        $this->port = $port;
        $this->encryption = $encryption;
        $this->username = $username;
        $this->password = $password;
    }

    public function connect(): bool
    {
        try {
            $protocol = $this->encryption === 'ssl' ? 'ssl' : 'tls';
            $mailbox = "{{$this->host}:{$this->port}/{$protocol}/novalidate-cert}INBOX";

            Log::info('Intentando conexión IMAP', [
                'host' => $this->host,
                'port' => $this->port,
                'encryption' => $this->encryption,
                'protocol' => $protocol,
                'mailbox' => $mailbox,
                'username' => $this->username,
            ]);

            $this->connection = @imap_open($mailbox, $this->username, $this->password);

            if ($this->connection === false) {
                $error = imap_last_error();
                $allErrors = imap_errors();

                Log::error('IMAP Connection failed - Detalles completos', [
                    'host' => $this->host,
                    'port' => $this->port,
                    'username' => $this->username,
                    'error' => $error,
                    'all_errors' => $allErrors,
                    'last_error' => imap_last_error(),
                ]);
                return false;
            }

            Log::info('IMAP Connection exitosa', [
                'host' => $this->host,
                'port' => $this->port,
                'username' => $this->username,
            ]);

            return true;
        } catch (\Exception $e) {
            Log::error('IMAP Connection exception', [
                'error' => $e->getMessage(),
                'host' => $this->host,
                'port' => $this->port,
                'username' => $this->username,
            ]);
            return false;
        }
    }

    public function disconnect(): void
    {
        if ($this->connection) {
            @imap_close($this->connection);
            $this->connection = null;
        }
    }

    public function searchCodes(PlatformSubject $subject): ?string
    {
        if (!$this->connection) {
            if (!$this->connect()) {
                return null;
            }
        }

        try {
            // Search for emails matching the subject
            $searchCriteria = 'SUBJECT "' . imap_utf8($subject->subject) . '"';
            $emails = @imap_search($this->connection, $searchCriteria, SE_UID);

            if (!$emails) {
                return null;
            }

            // Get the most recent email
            $latestEmailUid = max($emails);
            $overview = @imap_fetch_overview($this->connection, $latestEmailUid, FT_UID);

            if (!$overview || empty($overview)) {
                return null;
            }

            $emailBody = $this->getEmailBody($latestEmailUid);

            // Extract code using pattern if available
            $code = $this->extractCode($emailBody, $subject->pattern);

            if ($code) {
                return $code;
            }

            // Try to extract code from email body using common patterns
            return $this->extractCommonCodePatterns($emailBody);

        } catch (\Exception $e) {
            Log::error('IMAP Search error', [
                'error' => $e->getMessage(),
            ]);
            return null;
        }
    }

    private function getEmailBody(int $uid): string
    {
        try {
            $structure = @imap_fetchstructure($this->connection, $uid, FT_UID);

            if (!$structure) {
                return '';
            }

            return $this->getMessageBody($this->connection, $uid, $structure);
        } catch (\Exception $e) {
            return '';
        }
    }

    private function getMessageBody($connection, $uid, $structure): string
    {
        $body = '';

        if (empty($structure->parts)) {
            // Simple message
            $body = $this->getPart($connection, $uid, $structure, 0);
        } else {
            // Multipart message
            foreach ($structure->parts as $index => $part) {
                if ($part->subtype === 'PLAIN' || $part->subtype === 'HTML') {
                    $body = $this->getPart($connection, $uid, $part, $index + 1);
                    break;
                }
            }
        }

        return $body;
    }

    private function getPart($connection, $uid, $part, $partNumber): string
    {
        try {
            $data = @imap_fetchbody($connection, $uid, $partNumber, FT_UID);

            if ($part->encoding === 3) { // Base64
                $data = imap_base64($data);
            } elseif ($part->encoding === 4) { // Quoted-Printable
                $data = imap_qprint($data);
            }

            return $data ?: '';
        } catch (\Exception $e) {
            return '';
        }
    }

    private function extractCode(string $body, ?string $pattern): ?string
    {
        if (empty($body)) {
            return null;
        }

        if ($pattern) {
            if (preg_match($pattern, $body, $matches)) {
                return $matches[1] ?? $matches[0];
            }
        }

        return null;
    }

    private function extractCommonCodePatterns(string $body): ?string
    {
        if (empty($body)) {
            return null;
        }

        // Common patterns for verification codes
        $patterns = [
            // Netflix-like codes (4-6 digit codes)
            '/\b(\d{4,6})\b/',
            // Codes with spaces like "1 2 3 4 5 6"
            '/(\d\s*){4,6}/',
            // Alphanumeric codes
            '/\b([A-Z0-9]{6,12})\b/i',
            // URLs with verification codes
            '/verification\/([a-zA-Z0-9]+)/',
            // Reset codes
            '/code[:\s]+([A-Z0-9]+)/i',
            // Key patterns
            '/([A-Z0-9]{8}-[A-Z0-9]{4}-[A-Z0-9]{4}-[A-Z0-9]{4}-[A-Z0-9]{12})/i',
        ];

        foreach ($patterns as $pattern) {
            if (preg_match($pattern, $body, $matches)) {
                return trim($matches[1] ?? $matches[0]);
            }
        }

        return null;
    }

    public function getLastError(): string
    {
        return imap_last_error() ?: 'Unknown error';
    }
}
