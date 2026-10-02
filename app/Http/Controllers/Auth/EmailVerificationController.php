<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class EmailVerificationController extends Controller
{
    public function notice(Request $request): View|RedirectResponse
    {
        if ($request->user()?->hasVerifiedEmail()) {
            return redirect()->route($request->user()->canAccessCms() ? 'dashboard' : 'home');
        }

        return view('auth.verify-email');
    }

    public function verify(EmailVerificationRequest $request): RedirectResponse
    {
        $request->fulfill();

        AuditLog::record('auth.email_verified', 'Correo verificado.', 'ok', $request->user());

        return redirect()->route($request->user()->canAccessCms() ? 'dashboard' : 'home')
            ->with('status', $request->user()->canAccessCms()
                ? 'Correo verificado.'
                : 'Correo verificado. Un administrador debe habilitar el CMS para editar contenido.');
    }

    public function send(Request $request): RedirectResponse
    {
        if ($request->user()->hasVerifiedEmail()) {
            return redirect()->route($request->user()->canAccessCms() ? 'dashboard' : 'home');
        }

        $request->user()->sendEmailVerificationNotification();

        AuditLog::record('auth.verification_sent', 'Reenvío de correo de verificación.', 'ok', $request->user());

        return back()->with('status', 'Se envió un enlace de verificación.');
    }
}
