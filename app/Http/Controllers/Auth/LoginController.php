<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class LoginController extends Controller
{
    public function create(): View
    {
        return view('auth.login');
    }

    public function store(Request $request): RedirectResponse
    {
        $generic = 'No fue posible iniciar sesión.';

        $credentials = $request->validate([
            'email' => ['required', 'email', 'max:255'],
            'password' => ['required', 'string'],
        ]);

        $email = strtolower($credentials['email']);
        $user = User::query()->where('email', $email)->first();

        if (! Auth::attempt(['email' => $email, 'password' => $credentials['password']], $request->boolean('remember'))) {
            AuditLog::record('auth.login', 'Credenciales inválidas.', 'denied', $user);

            return back()->withErrors(['email' => $generic])->onlyInput('email');
        }

        $authenticated = $request->user();

        if (! $authenticated?->is_active || ! $authenticated->hasActiveRole()) {
            $this->deny($request, $authenticated, 'Cuenta inactiva o rol no válido.');

            return back()->withErrors(['email' => $generic])->onlyInput('email');
        }

        $request->session()->regenerate();

        AuditLog::record('auth.login', 'Inicio de sesión correcto.', 'ok', $authenticated);

        if ($authenticated->canAccessCms()) {
            return redirect()->intended(route('dashboard'));
        }

        return redirect()->route('home')->with(
            'status',
            'Inicio de sesión correcto. El CMS se habilita cuando un administrador lo autorice.'
        );
    }

    public function destroy(Request $request): RedirectResponse
    {
        $user = $request->user();

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        AuditLog::record('auth.logout', 'Cierre de sesión.', 'ok', $user);

        return redirect()->route('login');
    }

    private function deny(Request $request, ?User $user, string $description): void
    {
        AuditLog::record('auth.access_denied', $description, 'denied', $user);
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
    }
}
