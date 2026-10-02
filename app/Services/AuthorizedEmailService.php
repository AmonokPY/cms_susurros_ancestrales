<?php

namespace App\Services;

use App\Models\AuthorizedEmail;
use App\Models\User;
use Illuminate\Support\Collection;

class AuthorizedEmailService
{
    public function add(string $email, ?User $addedBy = null, ?string $reason = null): AuthorizedEmail
    {
        $email = strtolower(trim($email));

        return AuthorizedEmail::query()->updateOrCreate(
            ['email' => $email],
            [
                'added_by' => $addedBy?->id,
                'reason' => $reason,
                'is_active' => true,
            ]
        );
    }

    public function remove(string $email): void
    {
        AuthorizedEmail::query()
            ->where('email', strtolower(trim($email)))
            ->delete();
    }

    public function isAuthorized(string $email): bool
    {
        return AuthorizedEmail::isAuthorized($email);
    }

    public function listAll(): Collection
    {
        return AuthorizedEmail::query()
            ->with('addedBy')
            ->orderBy('email')
            ->get();
    }
}
