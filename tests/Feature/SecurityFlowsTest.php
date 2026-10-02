<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class SecurityFlowsTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_is_redirected_from_dashboard(): void
    {
        $this->get(route('dashboard'))->assertRedirect(route('login'));
    }

    public function test_login_rejects_invalid_credentials_with_generic_error(): void
    {
        $this->seed();

        $this->from(route('login'))
            ->post(route('login.store'), [
                'email' => 'usuario@secureapp.test',
                'password' => 'incorrecta',
            ])
            ->assertRedirect(route('login'))
            ->assertSessionHasErrors('email');
    }

    public function test_login_succeeds_and_reaches_dashboard(): void
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
}
