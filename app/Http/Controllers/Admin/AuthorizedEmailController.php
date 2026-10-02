<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreAuthorizedEmailRequest;
use App\Models\AuditLog;
use App\Models\AuthorizedEmail;
use App\Services\AuthorizedEmailService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class AuthorizedEmailController extends Controller
{
    public function index(AuthorizedEmailService $service): View
    {
        return view('admin.authorized-emails.index', [
            'emails' => $service->listAll(),
        ]);
    }

    public function store(StoreAuthorizedEmailRequest $request, AuthorizedEmailService $service): RedirectResponse
    {
        $service->add(
            $request->validated('email'),
            $request->user(),
            $request->validated('reason')
        );

        AuditLog::record('cms.authorized_email_added', $request->validated('email'), 'ok', $request->user());

        return back()->with('status', 'Correo autorizado.');
    }

    public function toggle(AuthorizedEmail $authorizedEmail): RedirectResponse
    {
        $authorizedEmail->update(['is_active' => ! $authorizedEmail->is_active]);

        AuditLog::record(
            'cms.authorized_email_toggled',
            $authorizedEmail->email,
            $authorizedEmail->is_active ? 'ok' : 'denied',
            request()->user()
        );

        return back()->with('status', 'Estado actualizado.');
    }

    public function destroy(AuthorizedEmail $authorizedEmail, AuthorizedEmailService $service): RedirectResponse
    {
        $email = $authorizedEmail->email;
        $service->remove($email);

        AuditLog::record('cms.authorized_email_removed', $email, 'ok', request()->user());

        return back()->with('status', 'Correo eliminado de la lista blanca.');
    }
}
