<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Task;
use App\Models\User;
use Faker\Factory as Faker;

class CommentSeeder extends Seeder
{
    public function run()
    {
        $faker = Faker::create();

        $tasks = Task::all();
        $users = User::all();

        foreach ($tasks as $task) {
            $commentsCount = rand(1, 5);

            for ($i = 1; $i <= $commentsCount; $i++) {
                $user = $users->random();

                $task->comments()->create([
                    'user_id' => $user->id,
                    'comment' => $faker->sentence,
                ]);
            }
        }
    }
}
