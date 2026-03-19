<?php

namespace Database\Seeders;

use App\Models\Activity;
use App\Models\Experience;
use App\Models\Post;
use App\Models\PostCategory;
use App\Models\PostTag;
use App\Models\Project;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $user = User::query()->updateOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Makogai Admin',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ],
        );

        if (PostCategory::query()->count() > 0 || PostTag::query()->count() > 0) {
            return;
        }

        $categories = PostCategory::factory()->count(5)->create();
        $tags = PostTag::factory()->count(12)->create();

        Post::factory()
            ->for($user)
            ->count(8)
            ->create()
            ->each(function (Post $post) use ($categories, $tags) {
                $post->category()->associate($categories->random());
                $post->save();

                $post->tags()->sync($tags->random(rand(2, 5))->pluck('id')->all());
            });

        Post::factory()
            ->for($user)
            ->featured()
            ->count(2)
            ->create()
            ->each(function (Post $post) use ($categories, $tags) {
                $post->category()->associate($categories->random());
                $post->save();

                $post->tags()->sync($tags->random(rand(2, 5))->pluck('id')->all());
            });

        Project::factory()
            ->for($user)
            ->count(9)
            ->create();

        Project::factory()
            ->for($user)
            ->featured()
            ->count(3)
            ->create();

        Activity::factory()
            ->for($user)
            ->count(40)
            ->create();

        Experience::factory()
            ->for($user)
            ->count(4)
            ->create();
    }
}
