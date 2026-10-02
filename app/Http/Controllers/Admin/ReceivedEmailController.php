<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ReceivedEmail;
use App\Services\MailReceiveService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Throwable;

class ReceivedEmailController extends Controller
{
    public function index(): View
    {
        return view('admin.emails.index', [
            'emails' => ReceivedEmail::query()->latest('received_at')->latest('id')->get(),
        ]);
    }

    public function show(ReceivedEmail $receivedEmail): View
    {
        if (! $receivedEmail->is_read) {
            $receivedEmail->update(['is_read' => true]);
        }

        return view('admin.emails.show', [
            'email' => $receivedEmail->fresh(),
        ]);
    }

    public function markRead(ReceivedEmail $receivedEmail): RedirectResponse
    {
        $receivedEmail->update(['is_read' => true]);

        return back()->with('status', 'Mensaje marcado como leído.');
    }

    public function fetch(MailReceiveService $inbox): RedirectResponse
    {
        try {
            $result = $inbox->pullInbox(20);
        } catch (Throwable $exception) {
            return back()->withErrors(['mailbox' => $exception->getMessage()]);
        }

        return back()->with(
            'status',
            "Buzón consultado. Nuevos: {$result['stored']} de {$result['fetched']} leídos."
        );
    }
}
