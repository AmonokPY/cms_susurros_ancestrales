<?php

namespace Tests\Feature;

use App\Services\MediaService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class ZapSecurityRemediationTest extends TestCase
{
    use RefreshDatabase;

    /**
     * [ALTO · CWE-89] Inyección SQL en POST /contacto (parámetro "name").
     * Verifica que el payload SQLi con caracteres numéricos/operadores ("ZAP OR 1=1 --")
     * sea rechazado con 422 por StoreContactRequest y que payloads alfabéticos con
     * sintaxis SQL ("ZAP' OR 'a'--") se almacenen como texto literal vía prepared statements.
     */
    public function test_contact_rejects_sql_injection_payload_and_stores_literals_safely(): void
    {
        Mail::fake();

        // 1. Payload con operadores/números SQL ("ZAP OR 1=1 --") debe rechazarse con 422
        $response = $this->postJson(route('contact.store'), [
            'name' => 'ZAP OR 1=1 --',
            'email' => 'zap@example.com',
            'message' => 'Mensaje de prueba de auditoria de seguridad ZAP.',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['name']);

        Mail::assertNothingSent();

        // 2. Payload con comillas y comentarios SQL que cumple caracteres alfabéticos ("ZAP' OR 'a'--")
        // se trata estrictamente como dato literal (prepared statements) y nunca ejecuta SQL.
        $literalPayload = "ZAP' OR 'a'--";

        $this->from(route('contact.create'))
            ->post(route('contact.store'), [
                'name' => $literalPayload,
                'email' => 'zap@example.com',
                'message' => 'Mensaje válido para verificar almacenamiento literal seguro.',
            ])
            ->assertRedirect(route('contact.create'))
            ->assertSessionHas('status');

        $this->assertDatabaseHas('audit_logs', [
            'action' => 'contact.send',
            'description' => sprintf('Mensaje de contacto enviado por %s.', e($literalPayload)),
            'result' => 'ok',
        ]);
    }

    /**
     * [ALTO · CWE-22] Path Traversal en POST /contacto (parámetro "name").
     * Envía "\\contacto" y "../../etc/passwd" esperando HTTP 422 / validación fallida.
     */
    public function test_contact_rejects_path_traversal_payloads_with_422(): void
    {
        Mail::fake();

        foreach (['\\contacto', '../../etc/passwd', '../storage/logs/laravel.log', 'Ana..Perez'] as $payload) {
            $this->postJson(route('contact.store'), [
                'name' => $payload,
                'email' => 'zap@example.com',
                'message' => 'Mensaje de prueba para validar rechazo de Path Traversal.',
            ])
                ->assertStatus(422)
                ->assertJsonValidationErrors(['name']);
        }

        Mail::assertNothingSent();
    }

    /**
     * [ALTO · CWE-22] MediaService valida que rutas con traversal no salgan de storage/app/public.
     */
    public function test_media_service_blocks_path_traversal(): void
    {
        $media = app(MediaService::class);

        $this->assertNull($media->url('../../.env'));
        $this->assertNull($media->url('..\\..\\config\\app.php'));
        $this->assertNull($media->resolveSafePublicStoragePath('../../.env'));
    }

    /**
     * [FASE 2 · CWE-497, CWE-693, CWE-1021, CWE-319]
     * Verifica la presencia de todas las cabeceras de seguridad en /, /contacto, /login y 404.
     */
    public function test_security_headers_are_present_on_public_auth_and_error_routes(): void
    {
        $this->seed();

        foreach (['/', '/contacto', '/login', '/ruta-inexistente-404'] as $uri) {
            $response = $this->get($uri);

            $response->assertHeader('X-Frame-Options', 'DENY');
            $response->assertHeader('X-Content-Type-Options', 'nosniff');
            $response->assertHeader('Strict-Transport-Security', 'max-age=31536000; includeSubDomains; preload');
            $response->assertHeader('Server', 'webserver');
            $response->assertHeaderMissing('X-Powered-By');

            $csp = (string) $response->headers->get('Content-Security-Policy');
            $this->assertStringContainsString("default-src 'self'", $csp);
            $this->assertStringContainsString("frame-ancestors 'none'", $csp);
            $this->assertStringContainsString("form-action 'self'", $csp);
            $this->assertStringContainsString('https://cdn.jsdelivr.net', $csp);
        }
    }

    /**
     * [MEDIO · CWE-345 / CWE-829] Verifica atributos SRI (integrity + crossorigin) en scripts CDN.
     */
    public function test_external_scripts_include_sri_integrity_attributes(): void
    {
        $this->seed();

        $response = $this->get('/');
        $response->assertOk();
        $response->assertSee('integrity="sha384-g4NTh/Iv5PPU4xPyhEWqPcwtNXOvdaDI8LLnyYfyNZOjKJeYQyjzQ9X5275eBjpt"', false);
        $response->assertSee('integrity="sha384-ioeiiA4Qj8TNkk15vf+DfcM4fQbw3fUabf9VWRr56yQZ2fjAWiHPV6oWH2Jo0bXE"', false);
        $response->assertSee('crossorigin="anonymous"', false);
    }
}
