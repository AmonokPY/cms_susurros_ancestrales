<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\RegisterUserRequest;
use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

/**
 * Controlador de registro de cuentas públicas.
 *
 * Remediación OWASP ZAP:
 * - [BAJO · CWE-201] Redirección 302 sin cuerpo HTML y con Content-Length: 0.
 */
class RegisterController extends Controller
{
    public function create(): View
    {
        return view('auth.register');
    }

    public function store(RegisterUserRequest $request): RedirectResponse
    {
        $user = User::create([
            'name' => $request->string('name'),
            'email' => $request->string('email'),
            'password' => Hash::make($request->string('password')),
            'role' => User::ROLE_USER,
            'is_active' => true,
            'cms_access' => false,
        ]);

        Auth::login($user);
        $request->session()->regenerate();

        AuditLog::record('auth.register', 'Registro de cuenta pública.', 'ok', $user);

        $redirect = redirect()->route('home')->with(
            'status',
            'Cuenta creada. Puede ver el sitio; el CMS lo habilita un administrador.'
        );
        $redirect->setContent('');
        $redirect->headers->set('Content-Length', '0');

        return $redirect;
    }
}
