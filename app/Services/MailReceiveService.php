<?php

namespace App\Services;

use App\Models\ReceivedEmail;
use App\Support\Html;
use Carbon\Carbon;
use Throwable;
use Webklex\IMAP\Facades\Client;
use Webklex\PHPIMAP\Message;

class MailReceiveService
{
    public function __construct(private readonly Pop3Client $pop3) {}

    /**
     * @return array{fetched: int, stored: int}
     */
    public function pullInbox(int $limit = 20): array
    {
        if (! config('imap.accounts.default.username') || ! config('imap.accounts.default.host')) {
            throw new \RuntimeException('El buzón no está configurado. Revise IMAP_* en .env.');
        }

        $protocol = strtolower((string) config('imap.accounts.default.protocol', 'imap'));

        if ($protocol === 'pop3') {
            return $this->pullPop3($limit);
        }

        return $this->pullImap($limit);
    }

    /**
     * @return array{fetched: int, stored: int}
     */
    private function pullPop3(int $limit): array
    {
        $messages = $this->pop3->fetch($limit);
        $stored = 0;

        foreach ($messages as $item) {
            $parsed = $this->pop3->parse($item['uid'], $item['raw']);
            $receivedAt = null;
            if (! empty($parsed['received_at'])) {
                try {
                    $receivedAt = Carbon::parse($parsed['received_at']);
                } catch (Throwable) {
                    $receivedAt = now();
                }
            }

            $email = ReceivedEmail::query()->firstOrCreate(
                ['message_id' => $parsed['message_id']],
                [
                    'from_email' => $parsed['from_email'],
                    'from_name' => $parsed['from_name'],
                    'subject' => $parsed['subject'],
                    'body' => $parsed['body'],
                    'received_at' => $receivedAt ?? now(),
                    'has_attachments' => $parsed['has_attachments'],
                ]
            );

            if ($email->wasRecentlyCreated) {
                $stored++;
            }
        }

        return ['fetched' => count($messages), 'stored' => $stored];
    }

    /**
     * @return array{fetched: int, stored: int}
     */
    private function pullImap(int $limit): array
    {
        $client = Client::account('default');
        $client->connect();

        $folder = $client->getFolder('INBOX');
        $messages = $folder->messages()->all()->limit($limit)->get();

        $stored = 0;
        $fetched = $messages->count();

        foreach ($messages as $message) {
            if ($this->persistImap($message)) {
                $stored++;
            }

            try {
                $message->setFlag(['Seen']);
            } catch (Throwable) {
                // Algunos servidores no aceptan flags.
            }
        }

        $client->disconnect();

        return ['fetched' => $fetched, 'stored' => $stored];
    }

    private function persistImap(Message $message): bool
    {
        $messageId = $this->stringValue($message->getMessageId());
        $from = $message->getFrom()[0] ?? null;
        $fromEmail = strtolower(trim((string) ($from->mail ?? 'unknown@unknown.local')));
        $fromName = $from->personal ?? null;
        $subject = $this->stringValue($message->getSubject());
        $text = (string) ($message->getTextBody() ?: '');
        $html = (string) ($message->getHTMLBody() ?: '');
        $body = $text !== '' ? $text : (string) Html::sanitize($html);
        $receivedAt = $message->getDate();

        if ($messageId === '') {
            $messageId = hash('sha256', $fromEmail.'|'.$subject.'|'.mb_substr($body, 0, 200));
        }

        $email = ReceivedEmail::query()->firstOrCreate(
            ['message_id' => mb_substr($messageId, 0, 255)],
            [
                'from_email' => mb_substr($fromEmail, 0, 255),
                'from_name' => $fromName ? mb_substr((string) $fromName, 0, 255) : null,
                'subject' => $subject !== '' ? mb_substr($subject, 0, 255) : '(sin asunto)',
                'body' => $body !== '' ? $body : null,
                'received_at' => $receivedAt instanceof Carbon ? $receivedAt : now(),
                'has_attachments' => $message->getAttachments()->count() > 0,
            ]
        );

        return $email->wasRecentlyCreated;
    }

    private function stringValue(mixed $value): string
    {
        if (is_object($value) && method_exists($value, 'toString')) {
            return trim((string) $value->toString());
        }

        return trim((string) $value);
    }
}
