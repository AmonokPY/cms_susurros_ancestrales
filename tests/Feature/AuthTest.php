<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_can_view_login_form(): void
    {
        $this->get(route('login'))
            ->assertOk()
            ->assertSee('Iniciar sesión');
    }

    public function test_guest_is_redirected_from_dashboard_to_login(): void
    {
        $this->get(route('dashboard'))
            ->assertRedirect(route('login'));
    }

    public function test_user_with_cms_access_logs_in_and_reaches_dashboard(): void
    {
        $user = $this->cmsUser([
            'email' => 'editor@example.com',
            'password' => Hash::make('Segura#2026!'),
        ]);

        $this->post(route('login.store'), [
            'email' => 'editor@example.com',
            'password' => 'Segura#2026!',
        ])
            ->assertRedirect(route('dashboard'));

        $this->assertAuthenticatedAs($user);
        $this->get(route('dashboard'))->assertOk();
    }

    public function test_invalid_credentials_show_generic_error(): void
    {
        $this->cmsUser([
            'email' => 'editor@example.com',
            'password' => Hash::make('Segura#2026!'),
        ]);

        $this->from(route('login'))
            ->post(route('login.store'), [
                'email' => 'editor@example.com',
                'password' => 'clave-incorrecta',
            ])
            ->assertRedirect(route('login'))
            ->assertSessionHasErrors('email');

        $this->assertGuest();
    }

    public function test_inactive_user_cannot_keep_a_session(): void
    {
        User::factory()->create([
            'email' => 'inactivo@example.com',
            'password' => Hash::make('Segura#2026!'),
            'is_active' => false,
            'role' => User::ROLE_EDITOR,
            'cms_access' => true,
        ]);

        $this->from(route('login'))
            ->post(route('login.store'), [
                'email' => 'inactivo@example.com',
                'password' => 'Segura#2026!',
            ])
            ->assertRedirect(route('login'))
            ->assertSessionHasErrors('email');

        $this->assertGuest();
    }

    public function test_authenticated_user_can_logout(): void
    {
        $user = $this->cmsUser();

        $this->actingAs($user)
            ->post(route('logout'))
            ->assertRedirect(route('login'));

        $this->assertGuest();
    }

    public function test_authenticated_cms_user_is_redirected_away_from_login(): void
    {
        $user = $this->cmsUser();

        $this->actingAs($user)
            ->get(route('login'))
            ->assertRedirect('/dashboard');
    }
}
