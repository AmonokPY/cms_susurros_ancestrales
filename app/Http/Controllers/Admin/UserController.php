<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\AuthorizedEmail;
use App\Models\User;
use App\Services\AuthorizedEmailService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class UserController extends Controller
{
    public function index(): View
    {
        $this->authorize('viewAny', User::class);

        return view('admin.users.index', [
            'users' => User::query()->latest()->get(),
        ]);
    }

    public function toggleCmsAccess(Request $request, User $user, AuthorizedEmailService $emails): RedirectResponse
    {
        $this->authorize('update', $user);

        $user->cms_access = ! $user->cms_access;

        if ($user->cms_access) {
            $user->role = User::ROLE_EDITOR;
            $emails->add($user->email, $request->user(), 'Habilitado como editor');
        } else {
            $user->role = User::ROLE_USER;
            AuthorizedEmail::query()->where('email', $user->email)->update(['is_active' => false]);
        }

        $user->save();

        AuditLog::record(
            $user->cms_access ? 'cms.user_access_granted' : 'cms.user_access_revoked',
            $user->email,
            $user->cms_access ? 'ok' : 'denied',
            $request->user()
        );

        return back()->with(
            'status',
            $user->cms_access
                ? 'La cuenta ahora es editor y puede entrar al CMS.'
                : 'La cuenta solo puede ver el sitio público.'
        );
    }

    public function destroy(Request $request, User $user): RedirectResponse
    {
        $this->authorize('delete', $user);

        $email = $user->email;
        AuthorizedEmail::query()->where('email', $email)->delete();
        $user->delete();

        AuditLog::record('cms.user_deleted', $email, 'ok', $request->user());

        return back()->with('status', 'Cuenta eliminada.');
    }
}
