<?php

namespace Tests;

use App\Models\AuthorizedEmail;
use App\Models\User;
use Illuminate\Foundation\Http\Middleware\ValidateCsrfToken;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->app['env'] = 'testing';

        $this->withoutMiddleware([
            ValidateCsrfToken::class,
            VerifyCsrfToken::class,
        ]);
    }

    protected function authorizeEmail(string $email): AuthorizedEmail
    {
        return AuthorizedEmail::query()->updateOrCreate(
            ['email' => strtolower($email)],
            ['reason' => 'test', 'is_active' => true]
        );
    }

    protected function cmsUser(array $attributes = []): User
    {
        $user = User::factory()->create(array_merge([
            'cms_access' => true,
            'role' => User::ROLE_EDITOR,
        ], $attributes));
        $this->authorizeEmail($user->email);

        return $user;
    }
}


