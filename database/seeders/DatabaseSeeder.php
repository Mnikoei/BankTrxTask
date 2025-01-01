<?php

namespace Database\Seeders;

 use App\Services\AccessLevel\Database\Seeders\PermissionsTableSeeder;
 use App\Services\AccessLevel\Database\Seeders\RolesTableSeeder;
 use App\Services\User\Models\User;
 use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Services\AccessLevel\Database\Seeders\RolePermissionTableSeeder;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::factory()->create([
            'username' => 'admin-user',
            'email' => 'test@example.com',
        ]);
    }
}
