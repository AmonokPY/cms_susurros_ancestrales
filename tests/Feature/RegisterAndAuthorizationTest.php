<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RegisterAndAuthorizationTest extends TestCase
{
    use RefreshDatabase;

    public function test_editor_cannot_manage_users(): void
    {
        $editor = $this->cmsUser(['role' => User::ROLE_EDITOR]);

        $this->actingAs($editor)
            ->get(route('admin.users.index'))
            ->assertForbidden();

        $this->actingAs($editor)
            ->get(route('admin.play-items.index'))
            ->assertOk();
    }

    public function test_admin_can_list_users(): void
    {
        $admin = $this->cmsUser(['role' => User::ROLE_ADMIN]);

        $this->actingAs($admin)
            ->get(route('admin.users.index'))
            ->assertOk();
    }

    public function test_guide_register_url_redirects_to_existing_route(): void
    {
        $this->get('/register')->assertRedirect('/registro');
    }
}
