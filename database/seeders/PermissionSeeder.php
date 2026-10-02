<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Seeder;

class PermissionSeeder extends Seeder
{
    public function run(): void
    {
        $permissions = [
            ['Gestionar usuarios', 'manage-users'],
            ['Gestionar contenido', 'manage-content'],
            ['Gestionar multimedia', 'manage-media'],
        ];

        foreach ($permissions as [$name, $slug]) {
            Permission::query()->updateOrCreate(['slug' => $slug], ['name' => $name]);
        }

        $map = [
            'admin' => ['manage-users', 'manage-content', 'manage-media'],
            'editor' => ['manage-content', 'manage-media'],
            'autor' => ['manage-content'],
            'usuario' => [],
        ];

        foreach ($map as $roleSlug => $slugs) {
            $role = Role::query()->where('slug', $roleSlug)->first();
            if (! $role) {
                continue;
            }

            $ids = Permission::query()->whereIn('slug', $slugs)->pluck('id');
            $role->permissions()->sync($ids);
        }
    }
}
