<?php

namespace Database\Seeders;


use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // \App\Models\User::factory(10)->create();
        $role = Role::create(['name' => 'admin']);
        $admin_user = User::create([
            'name' => 'Admin',
            'email' => 'admin@admin.com',
            'password' => bcrypt('password')
        ]);
        $admin_user->assignRole($role);

        $user_role = Role::create(['name' => 'user']);
        $_user = User::create([
            'name' => 'Amal',
            'email' => 'amal@amal.com',
            'password' => bcrypt('password')
        ]);
        $_user->assignRole($user_role);
    }
}