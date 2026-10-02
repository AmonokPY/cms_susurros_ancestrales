<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserCmsAccessTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_grant_and_revoke_cms_access(): void
    {
        $admin = $this->cmsUser(['role' => User::ROLE_ADMIN]);
        $member = User::factory()->create(['email' => 'miembro@example.com']);

        $this->actingAs($admin)
            ->patch(route('admin.users.toggle-cms', $member))
            ->assertRedirect();

        $member->refresh();
        $this->assertTrue($member->cms_access);
        $this->assertTrue($member->canAccessCms());

        $this->actingAs($member)
            ->get(route('dashboard'))
            ->assertOk();

        $this->actingAs($admin)
            ->patch(route('admin.users.toggle-cms', $member))
            ->assertRedirect();

        $member->refresh();
        $this->assertFalse($member->canAccessCms());

        $this->actingAs($member)
            ->get(route('dashboard'))
            ->assertRedirect(route('home'));
    }

    public function test_admin_can_delete_a_user(): void
    {
        $admin = $this->cmsUser(['role' => User::ROLE_ADMIN]);
        $member = User::factory()->create(['email' => 'borrar@example.com']);

        $this->actingAs($admin)
            ->delete(route('admin.users.destroy', $member))
            ->assertRedirect();

        $this->assertDatabaseMissing('users', ['email' => 'borrar@example.com']);
    }

    public function test_admin_cannot_toggle_own_cms_access(): void
    {
        $admin = $this->cmsUser(['role' => User::ROLE_ADMIN]);

        $this->actingAs($admin)
            ->patch(route('admin.users.toggle-cms', $admin))
            ->assertForbidden();
    }
}
