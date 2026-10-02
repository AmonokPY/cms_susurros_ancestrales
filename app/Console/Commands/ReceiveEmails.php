<?php

namespace App\Console\Commands;

use App\Services\MailReceiveService;
use Illuminate\Console\Command;
use Throwable;

class ReceiveEmails extends Command
{
    protected $signature = 'mail:receive {--limit=20 : Máximo de mensajes a consultar}';

    protected $description = 'Consulta el buzón POP3/IMAP y guarda mensajes nuevos';

    public function handle(MailReceiveService $inbox): int
    {
        try {
            $result = $inbox->pullInbox((int) $this->option('limit'));
        } catch (Throwable $exception) {
            $this->error($exception->getMessage());

            return self::FAILURE;
        }

        $this->info("Consultados: {$result['fetched']}. Nuevos guardados: {$result['stored']}.");

        return self::SUCCESS;
    }
}
