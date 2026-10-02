<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MiddlewareTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_cannot_open_dashboard(): void
    {
        $this->get(route('dashboard'))->assertRedirect(route('login'));
    }

    public function test_verified_user_without_cms_access_stays_on_public_site(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get(route('dashboard'))
            ->assertRedirect(route('home'));

        $this->assertAuthenticated();
    }

    public function test_verified_authorized_user_opens_dashboard(): void
    {
        $user = $this->cmsUser();

        $this->actingAs($user)
            ->get(route('dashboard'))
            ->assertOk();
    }
}
