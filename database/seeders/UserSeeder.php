<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserSeeder extends Seeder
{
    public function run()
    {
        // Default password
        $defaultPassword = app()->environment('production') ? Str::random() : '12345678';
        $this->command->getOutput()->writeln("<info>Default password:</info> $defaultPassword");

        // Create super admin user
        $user     = new User();
        $role     = new Role();

        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        $user->truncate();
        $role->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // Create the four user roles
        $customerRole = Role::create(['name' => 'customer']);
        $developerRole = Role::create(['name' => 'developer']);
        $projectManagerRole = Role::create(['name' => 'project_manager']);
        $adminRole = Role::create(['name' => 'administrator']);

        $permissions = config('permissions');
        // Assign the permissions to each role
        foreach ($permissions['customer'] as $key => $permission) {
            $customerRole->permissions()->create(['name' => $key]);
        }

        foreach ($permissions['developer'] as $key => $permission) {
            $developerRole->permissions()->create(['name' => $key]);
        }

        foreach ($permissions['project_manager'] as $key => $permission) {
            $projectManagerRole->permissions()->create(['name' => $key]);
        }

        foreach ($permissions['administrator'] as $key => $permission) {
            $adminRole->permissions()->create(['name' => $key]);
        }

        // Create admin user
        $superAdmin = $user->create([
            'name' => 'Super Admin',
            'email' => 'admin@example.com',
            'password' => Hash::make($defaultPassword),
            'role_id' => $adminRole->id
        ]);

        $superAdmin->api_token = $superAdmin->createToken('admin@example.com')->plainTextToken;
        $superAdmin->save();

        // Create developer user
        $developer = $user->create([
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => Hash::make($defaultPassword),
            'role_id' => $developerRole->id,
        ]);

        $developer->api_token = $developer->createToken('john@example.com')->plainTextToken;
        $developer->save();

        // Create project manager user
        $projectManager = $user->create([
            'name' => 'Jane Doe',
            'email' => 'jane@example.com',
            'password' => Hash::make($defaultPassword),
            'role_id' => $projectManagerRole->id
        ]);

        $projectManager->api_token = $projectManager->createToken('jane@example.com')->plainTextToken;
        $projectManager->save();

        // Create customer user
        $customer = $user->create([
            'name' => 'Alice Smith',
            'email' => 'alice@example.com',
            'password' => Hash::make($defaultPassword),
            'role_id' => $customerRole->id
        ]);

        $customer->api_token = $customer->createToken('alice@example.com')->plainTextToken;
        $customer->save();
    }
}
