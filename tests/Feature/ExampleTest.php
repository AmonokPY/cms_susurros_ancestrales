<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    use RefreshDatabase;

    public function test_the_application_returns_a_successful_response(): void
    {
        $this->seed();

        $this->get('/')->assertOk();
        $this->get('/acerca')->assertOk();
        $this->get('/contacto')->assertOk();
        $this->get('/puzzles')->assertOk();
        $this->get('/puzzles/farallones-de-suta-tausa')->assertOk();
        $this->get('/dashboard')->assertRedirect(route('login'));
    }
}
