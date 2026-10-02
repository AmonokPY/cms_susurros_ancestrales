<?php

namespace App\Services;

use RuntimeException;

class Pop3Client
{
    /**
     * @return list<array{uid: string, raw: string}>
     */
    public function fetch(int $limit = 20): array
    {
        $host = (string) config('imap.accounts.default.host');
        $port = (int) config('imap.accounts.default.port', 1100);
        $username = (string) config('imap.accounts.default.username');
        $password = (string) config('imap.accounts.default.password');

        $fp = @stream_socket_client(
            'tcp://'.$host.':'.$port,
            $errno,
            $errstr,
            20
        );

        if (! $fp) {
            throw new RuntimeException("No se pudo conectar a POP3 ($host:$port): $errstr");
        }

        stream_set_timeout($fp, 20);
        $this->expectOk($fp, 'banner');
        $this->command($fp, 'USER '.$username);
        $this->command($fp, 'PASS '.$password);

        $list = $this->multiline($fp, 'LIST');
        $ids = [];
        foreach (preg_split('/\r?\n/', $list) as $line) {
            if (preg_match('/^(\d+)\s+(\d+)/', trim($line), $m)) {
                $ids[] = ['num' => (int) $m[1], 'uid' => 'pop3-'.$m[1].'-'.$m[2]];
            }
        }

        $ids = array_slice($ids, -1 * $limit);
        $messages = [];

        foreach ($ids as $item) {
            $raw = $this->multiline($fp, 'RETR '.$item['num']);
            $messages[] = [
                'uid' => $item['uid'],
                'raw' => $raw,
            ];
        }

        fwrite($fp, "QUIT\r\n");
        fclose($fp);

        return $messages;
    }

    /**
     * @return array{message_id: string, from_email: string, from_name: ?string, subject: string, body: string, received_at: ?string, has_attachments: bool}
     */
    public function parse(string $uid, string $raw): array
    {
        $raw = str_replace("\r\n", "\n", $raw);
        $parts = preg_split("/\n\n/", $raw, 2);
        $headerBlock = $parts[0] ?? '';
        $body = $parts[1] ?? '';

        $headers = $this->headers($headerBlock);
        $from = $headers['from'] ?? '';
        $fromEmail = 'unknown@unknown.local';
        $fromName = null;

        if (preg_match('/<([^>]+)>/', $from, $m)) {
            $fromEmail = strtolower($m[1]);
            $fromName = trim(trim(str_replace($m[0], '', $from)), '"');
        } elseif (filter_var(trim($from), FILTER_VALIDATE_EMAIL)) {
            $fromEmail = strtolower(trim($from));
        }

        $messageId = trim($headers['message-id'] ?? '');
        if ($messageId === '') {
            $messageId = $uid !== '' ? $uid : hash('sha256', $raw);
        }

        $subject = $headers['subject'] ?? '(sin asunto)';
        if (function_exists('mb_decode_mimeheader')) {
            $subject = mb_decode_mimeheader($subject);
        }

        $hasAttachments = (bool) preg_match('/^content-type:\s*multipart\//im', $headerBlock);

        if ($hasAttachments && preg_match('/Content-Type:\s*text\/plain[^\n]*\n(?:Content-[^\n]+\n)*\n(.*?)(?:\n--|\z)/is', $body, $textPart)) {
            $body = trim($textPart[1]);
        }

        $body = trim(strip_tags($body));

        return [
            'message_id' => mb_substr($messageId, 0, 255),
            'from_email' => mb_substr($fromEmail, 0, 255),
            'from_name' => $fromName !== null && $fromName !== '' ? mb_substr($fromName, 0, 255) : null,
            'subject' => mb_substr($subject, 0, 255),
            'body' => $body !== '' ? $body : null,
            'received_at' => $headers['date'] ?? null,
            'has_attachments' => $hasAttachments,
        ];
    }

    private function command($fp, string $line): string
    {
        fwrite($fp, $line."\r\n");

        return $this->expectOk($fp, $line);
    }

    private function expectOk($fp, string $context): string
    {
        $response = fgets($fp);
        if ($response === false || ! str_starts_with($response, '+OK')) {
            throw new RuntimeException('POP3 rechazó '.$context.': '.trim((string) $response));
        }

        return $response;
    }

    private function multiline($fp, string $command): string
    {
        $this->command($fp, $command);
        $buffer = '';

        while (($line = fgets($fp)) !== false) {
            if ($line === ".\r\n" || $line === ".\n") {
                break;
            }
            if (str_starts_with($line, '..')) {
                $line = substr($line, 1);
            }
            $buffer .= $line;
        }

        return $buffer;
    }

    /**
     * @return array<string, string>
     */
    private function headers(string $block): array
    {
        $block = preg_replace("/\n[ \t]+/", ' ', $block) ?? $block;
        $headers = [];

        foreach (preg_split('/\n/', $block) as $line) {
            if (! str_contains($line, ':')) {
                continue;
            }
            [$name, $value] = explode(':', $line, 2);
            $headers[strtolower(trim($name))] = trim($value);
        }

        return $headers;
    }
}
