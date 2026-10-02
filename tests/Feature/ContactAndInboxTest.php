<?php

namespace Tests\Feature;

use App\Mail\ContactMessage;
use App\Models\ReceivedEmail;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class ContactAndInboxTest extends TestCase
{
    use RefreshDatabase;

    public function test_contact_page_is_public(): void
    {
        $this->seed();

        $this->get(route('contact.create'))
            ->assertOk()
            ->assertSee('Enviar mensaje');
    }

    public function test_contact_form_sends_mailable(): void
    {
        Mail::fake();

        $this->from(route('contact.create'))
            ->post(route('contact.store'), [
                'name' => 'Ana Perez',
                'email' => 'ana@example.com',
                'message' => 'Quiero jugar Susurros Ancestrales en Tausa.',
            ])
            ->assertRedirect()
            ->assertSessionHas('status');

        Mail::assertSent(ContactMessage::class, function (ContactMessage $mail) {
            return $mail->senderEmail === 'ana@example.com'
                && $mail->hasReplyTo('ana@example.com');
        });
    }

    public function test_contact_rejects_empty_message(): void
    {
        Mail::fake();

        $this->from(route('contact.create'))
            ->post(route('contact.store'), [
                'name' => 'Ana',
                'email' => 'ana@example.com',
                'message' => '',
            ])
            ->assertSessionHasErrors('message');

        Mail::assertNothingSent();
    }

    public function test_guest_cannot_open_inbox(): void
    {
        $this->get(route('admin.emails.index'))->assertRedirect(route('login'));
    }

    public function test_editor_can_list_and_read_received_email(): void
    {
        $editor = $this->cmsUser(['role' => User::ROLE_EDITOR]);
        $email = ReceivedEmail::query()->create([
            'message_id' => '<demo@cms.test>',
            'from_email' => 'visitante@example.com',
            'from_name' => 'Visitante',
            'subject' => 'Hola CMS',
            'body' => 'Texto plano',
            'received_at' => now(),
            'is_read' => false,
        ]);

        $this->actingAs($editor)
            ->get(route('admin.emails.index'))
            ->assertOk()
            ->assertSee('Hola CMS');

        $this->actingAs($editor)
            ->get(route('admin.emails.show', $email))
            ->assertOk()
            ->assertSee('Texto plano');

        $this->assertTrue($email->fresh()->is_read);
    }

    public function test_message_id_is_unique(): void
    {
        ReceivedEmail::query()->create([
            'message_id' => '<dup@cms.test>',
            'from_email' => 'a@example.com',
            'subject' => 'Uno',
            'received_at' => now(),
        ]);

        $this->expectException(\Illuminate\Database\QueryException::class);

        ReceivedEmail::query()->create([
            'message_id' => '<dup@cms.test>',
            'from_email' => 'b@example.com',
            'subject' => 'Dos',
            'received_at' => now(),
        ]);
    }
}
