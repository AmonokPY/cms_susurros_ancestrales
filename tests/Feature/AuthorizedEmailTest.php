<?php

namespace Tests\Feature;

use App\Models\AuthorizedEmail;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthorizedEmailTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_add_list_toggle_and_remove_authorized_email(): void
    {
        $admin = $this->cmsUser(['role' => 'admin']);

        $this->actingAs($admin)
            ->post(route('admin.authorized-emails.store'), [
                'email' => 'nuevo@example.com',
                'reason' => 'Equipo',
            ])
            ->assertRedirect();

        $this->assertTrue(AuthorizedEmail::isAuthorized('nuevo@example.com'));

        $this->actingAs($admin)
            ->get(route('admin.authorized-emails.index'))
            ->assertOk()
            ->assertSee('nuevo@example.com');

        $item = AuthorizedEmail::query()->where('email', 'nuevo@example.com')->first();

        $this->actingAs($admin)
            ->patch(route('admin.authorized-emails.toggle', $item))
            ->assertRedirect();

        $this->assertFalse(AuthorizedEmail::isAuthorized('nuevo@example.com'));

        $item->refresh();

        $this->actingAs($admin)
            ->delete(route('admin.authorized-emails.destroy', $item))
            ->assertRedirect();

        $this->assertDatabaseMissing('authorized_emails', ['email' => 'nuevo@example.com']);
    }

    public function test_editor_cannot_manage_whitelist(): void
    {
        $editor = $this->cmsUser(['role' => 'editor']);

        $this->actingAs($editor)
            ->get(route('admin.authorized-emails.index'))
            ->assertForbidden();
    }
}
