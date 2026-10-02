<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RegisterTest extends TestCase
{
    use RefreshDatabase;

    public function test_real_email_registers_as_public_user(): void
    {
        $this->post(route('register.store'), [
            'name' => 'Ana Perez',
            'email' => 'ana@example.com',
            'password' => 'ClaveSegura1',
            'password_confirmation' => 'ClaveSegura1',
        ])->assertRedirect(route('home'));

        $this->assertAuthenticated();
        $this->assertDatabaseHas('users', [
            'email' => 'ana@example.com',
            'role' => User::ROLE_USER,
            'cms_access' => 0,
        ]);
        $this->get(route('dashboard'))->assertRedirect(route('home'));
    }

    public function test_rejects_disposable_email(): void
    {
        $this->from(route('register'))
            ->post(route('register.store'), [
                'name' => 'Ana Perez',
                'email' => 'demo@mailinator.com',
                'password' => 'ClaveSegura1',
                'password_confirmation' => 'ClaveSegura1',
            ])
            ->assertSessionHasErrors('email');
    }

    public function test_rejects_weak_password(): void
    {
        $this->from(route('register'))
            ->post(route('register.store'), [
                'name' => 'Ana Perez',
                'email' => 'ana@example.com',
                'password' => 'secret',
                'password_confirmation' => 'secret',
            ])
            ->assertSessionHasErrors('password');
    }

    public function test_rejects_password_containing_name(): void
    {
        $this->from(route('register'))
            ->post(route('register.store'), [
                'name' => 'AnaPerez',
                'email' => 'ana@example.com',
                'password' => 'AnaPerez1',
                'password_confirmation' => 'AnaPerez1',
            ])
            ->assertSessionHasErrors('password');
    }
}
