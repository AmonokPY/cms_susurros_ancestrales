<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreContactRequest;
use App\Mail\ContactMessage;
use App\Models\AuditLog;
use App\Models\SiteSetting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\View\View;
use Throwable;

class ContactController extends Controller
{
    public function create(): View
    {
        return view('contact.create', [
            'settings' => SiteSetting::current(),
        ]);
    }

    public function store(StoreContactRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $to = (string) config('mail.contact_to');

        try {
            Mail::to($to)->send(new ContactMessage(
                $data['name'],
                $data['email'],
                $data['message'],
            ));
        } catch (Throwable $exception) {
            Log::warning('contact.mail_failed', ['error' => $exception->getMessage()]);
            AuditLog::record('contact.send', 'Fallo al enviar el formulario de contacto.', 'denied');

            return back()
                ->withInput()
                ->withErrors(['message' => 'No fue posible enviar el mensaje. Intente más tarde.']);
        }

        AuditLog::record('contact.send', 'Mensaje de contacto enviado.', 'ok');

        return back()->with('status', 'Mensaje enviado correctamente.');
    }
}
