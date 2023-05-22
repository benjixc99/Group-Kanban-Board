<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Project;
use App\Models\Role;

class ProjectUserSeeder extends Seeder
{
    public function run()
    {
        // Assign users to projects with roles
        $users = User::all();
        $projects = Project::all();
        $roles = Role::all();

        foreach ($projects as $project) {
            foreach ($users as $user) {
                $role = $roles->random();

                $project->users()->attach($user, ['role_id' => $role->id]);
            }
        }
    }
}
