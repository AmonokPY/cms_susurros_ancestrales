<?php

namespace Tests\Feature;

use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PostAuthorizationTest extends TestCase
{
    use RefreshDatabase;

    public function test_owner_can_update_post_and_stranger_cannot(): void
    {
        $owner = User::factory()->create();
        $stranger = User::factory()->create();
        $post = Post::factory()->create(['user_id' => $owner->id]);

        $this->actingAs($stranger)
            ->patch(route('posts.update', $post), [
                'title' => 'Intento ajeno',
                'body' => 'No debería guardarse.',
            ])
            ->assertForbidden();

        $this->actingAs($owner)
            ->patch(route('posts.update', $post), [
                'title' => 'Titulo actualizado',
                'body' => 'Contenido actualizado por el propietario.',
            ])
            ->assertRedirect(route('posts.show', $post));

        $this->assertDatabaseHas('posts', [
            'id' => $post->id,
            'title' => 'Titulo actualizado',
        ]);
    }
}
