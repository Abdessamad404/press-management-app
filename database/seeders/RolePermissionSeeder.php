<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;

class RolePermissionSeeder extends Seeder
{
    public function run()
    {
        // Créer les permissions
        Permission::firstOrCreate(['name' => 'create articles']);
        Permission::firstOrCreate(['name' => 'edit own articles']);
        Permission::firstOrCreate(['name' => 'delete own articles']);
        Permission::firstOrCreate(['name' => 'view all articles']);
        Permission::firstOrCreate(['name' => 'approve articles']);
        Permission::firstOrCreate(['name' => 'reject articles']);
        Permission::firstOrCreate(['name' => 'edit all articles']);

        // Créer les rôles
        $writerRole = Role::firstOrCreate(['name' => 'writer']);
        $editorRole = Role::firstOrCreate(['name' => 'editor']);

        // Assigner permissions aux rôles
        $writerRole->givePermissionTo([
            'create articles',
            'edit own articles',
            'delete own articles'
        ]);

        $editorRole->givePermissionTo([
            'view all articles',
            'approve articles',
            'reject articles',
            'edit all articles'
        ]);

        // Créer utilisateurs de test
        $writer = User::firstOrCreate([
            'email' => 'writer@test.com'
        ], [
            'name' => 'Rédacteur Test',
            'password' => bcrypt('password'),
        ]);
        $writer->assignRole('writer');

        $editor = User::firstOrCreate([
            'email' => 'editor@test.com'
        ], [
            'name' => 'Éditeur Test',
            'password' => bcrypt('password'),
        ]);
        $editor->assignRole('editor');
    }
}