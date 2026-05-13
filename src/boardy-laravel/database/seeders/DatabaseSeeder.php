<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Post;
use App\Models\Comment;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        User::factory()->create([
            'name' => 'Тест',
            'email' => 'test@boardy.local',
            'password' => bcrypt('password'),
        ]);

        $users = User::factory()->count(4)->create();

        Post::factory()->count(10)->create([
            'author_id' => fn() => User::all()->random()->id,
        ]);

        Comment::factory()->count(25)->create([
            'post_id' => fn() => Post::all()->random()->id,
            'author_id' => fn() => User::all()->random()->id,
        ]);
    }
}
