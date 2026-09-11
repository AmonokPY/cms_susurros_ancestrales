<?php

namespace Tests\Feature;

use App\Models\User;
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
        $user = User::factory()->create([
            'password' => Hash::make('Segura#2026!'),
        ]);

        $this->post(route('login.store'), [
            'email' => $user->email,
            'password' => 'Segura#2026!',
        ])->assertRedirect(route('dashboard'));

        $this->get(route('dashboard'))->assertOk();
    }

    public function test_contact_form_validates_on_server(): void
    {
        $this->from(route('contact'))
            ->post(route('contact.send'), [])
            ->assertSessionHasErrors(['name', 'email', 'message']);

        $this->from(route('contact'))
            ->post(route('contact.send'), [
                'name' => 'María',
                'email' => 'maria@colombia.co',
                'message' => 'Hola, quiero conocer el proyecto.',
            ])
            ->assertRedirect()
            ->assertSessionHas('status');
    }

    public function test_posts_require_authentication(): void
    {
        $this->get(route('posts.index'))->assertRedirect(route('login'));
    }
}
