<?php

// database/seeders/PermissionSeeder.php

use App\Models\Permission;

class PermissionSeeder extends Seeder
{
    public function run(): void
    {
        $permissions = [
            ['usuarios.view', 'Visualizar usuários', 'usuarios'],
            ['usuarios.create', 'Criar usuários', 'usuarios'],
            ['usuarios.edit', 'Editar usuários', 'usuarios'],
            ['usuarios.delete', 'Excluir usuários', 'usuarios'],

            ['perfis.view', 'Visualizar perfis', 'perfis'],
            ['perfis.create', 'Criar perfis', 'perfis'],
            ['perfis.edit', 'Editar perfis', 'perfis'],
            ['perfis.delete', 'Excluir perfis', 'perfis'],
        ];

        foreach ($permissions as $p) {
            Permission::create([
                'name' => $p[0],
                'label' => $p[1],
                'module' => $p[2],
            ]);
        }
    }
}