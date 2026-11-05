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

/**
 * Controlador del formulario público de contacto (GET/POST /contacto).
 *
 * Remediación OWASP ZAP:
 * - [ALTO · CWE-89] Garantiza que "name", "email" y "message" se procesen exclusivamente
 *   como datos parametrizados mediante Eloquent prepared statements (AuditLog) y nunca
 *   mediante concatenación SQL o DB::raw.
 * - [ALTO · CWE-22] Sanitiza y bloquea cualquier intento de construcción de rutas con "name".
 */
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
        $safeName = trim(str_replace(["\0", '/', '\\', '..'], '', (string) $data['name']));
        $safeEmail = trim((string) $data['email']);
        $safeMessage = (string) $data['message'];
        $to = (string) config('mail.contact_to');

        try {
            Mail::to($to)->send(new ContactMessage(
                $safeName,
                $safeEmail,
                $safeMessage,
            ));
        } catch (Throwable $exception) {
            Log::warning('contact.mail_failed', ['error' => $exception->getMessage()]);
            AuditLog::record('contact.send', 'Fallo al enviar el formulario de contacto.', 'denied');

            return back()
                ->withInput()
                ->withErrors(['message' => 'No fue posible enviar el mensaje. Intente más tarde.']);
        }

        AuditLog::record(
            'contact.send',
            sprintf('Mensaje de contacto enviado por %s.', e($safeName)),
            'ok'
        );

        return back()->with('status', 'Mensaje enviado correctamente.');
    }
}
