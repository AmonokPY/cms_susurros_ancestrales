<?php

namespace Tests\Feature;

use App\Models\User;
use App\Policies\UserPolicy;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserPolicyTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_view_any_users(): void
    {
        $admin = User::factory()->admin()->create();

        $this->assertTrue($admin->can('viewAny', User::class));
    }

    public function test_editor_cannot_view_any_users(): void
    {
        $editor = User::factory()->withCmsAccess()->create();

        $this->assertFalse($editor->can('viewAny', User::class));
    }

    public function test_regular_user_cannot_view_any_users(): void
    {
        $user = User::factory()->create();

        $this->assertFalse($user->can('viewAny', User::class));
    }

    public function test_admin_can_update_another_user(): void
    {
        $admin = User::factory()->admin()->create();
        $member = User::factory()->create();

        $this->assertTrue($admin->can('update', $member));
    }

    public function test_admin_cannot_update_own_account_via_policy(): void
    {
        $admin = User::factory()->admin()->create();

        $this->assertFalse($admin->can('update', $admin));
    }

    public function test_admin_can_delete_a_regular_user(): void
    {
        $admin = User::factory()->admin()->create();
        $member = User::factory()->create();

        $this->assertTrue($admin->can('delete', $member));
    }

    public function test_admin_cannot_delete_own_account(): void
    {
        $admin = User::factory()->admin()->create();

        $this->assertFalse($admin->can('delete', $admin));
    }

    public function test_admin_cannot_delete_another_admin(): void
    {
        $admin = User::factory()->admin()->create();
        $otherAdmin = User::factory()->admin()->create();

        $this->assertFalse($admin->can('delete', $otherAdmin));
    }

    public function test_editor_cannot_delete_users(): void
    {
        $editor = User::factory()->withCmsAccess()->create();
        $member = User::factory()->create();

        $this->assertFalse($editor->can('delete', $member));
    }

    public function test_policy_class_denies_update_for_non_admin(): void
    {
        $policy = new UserPolicy;
        $editor = User::factory()->withCmsAccess()->create();
        $member = User::factory()->create();

        $this->assertFalse($policy->update($editor, $member));
    }
}
