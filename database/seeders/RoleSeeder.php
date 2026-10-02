<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        foreach ([
            ['Administrador', 'admin'],
            ['Editor', 'editor'],
            ['Autor', 'autor'],
            ['Usuario', 'usuario'],
        ] as [$name, $slug]) {
            Role::query()->updateOrCreate(['slug' => $slug], ['name' => $name]);
        }
    }
}
