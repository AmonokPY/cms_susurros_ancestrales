<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class LoginTest extends TestCase
{
    use RefreshDatabase;

    public function test_valid_verified_authorized_user_reaches_dashboard(): void
    {
        $user = $this->cmsUser([
            'password' => Hash::make('Segura#2026!'),
        ]);

        $this->post(route('login.store'), [
            'email' => $user->email,
            'password' => 'Segura#2026!',
        ])->assertRedirect(route('dashboard'));

        $this->get(route('dashboard'))->assertOk();
    }

    public function test_invalid_credentials_use_generic_error(): void
    {
        $user = $this->cmsUser([
            'password' => Hash::make('Segura#2026!'),
        ]);

        $this->from(route('login'))
            ->post(route('login.store'), [
                'email' => $user->email,
                'password' => 'incorrecta',
            ])
            ->assertRedirect(route('login'))
            ->assertSessionHasErrors('email');
    }

    public function test_user_without_cms_access_logs_in_to_public_site(): void
    {
        $user = User::factory()->create([
            'password' => Hash::make('Segura#2026!'),
        ]);

        $this->post(route('login.store'), [
            'email' => $user->email,
            'password' => 'Segura#2026!',
        ])->assertRedirect(route('home'));

        $this->assertAuthenticated();
        $this->get(route('dashboard'))->assertRedirect(route('home'));
        $this->get(route('home'))->assertOk();
    }

    public function test_login_is_rate_limited_after_five_attempts(): void
    {
        for ($i = 0; $i < 5; $i++) {
            $this->post(route('login.store'), [
                'email' => 'nadie@example.com',
                'password' => 'x',
            ]);
        }

        $this->post(route('login.store'), [
            'email' => 'nadie@example.com',
            'password' => 'x',
        ])->assertStatus(429);
    }
}
