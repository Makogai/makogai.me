<?php

namespace Database\Seeders;

use App\Models\Activity;
use App\Models\Experience;
use App\Models\Post;
use App\Models\PostCategory;
use App\Models\PostTag;
use App\Models\Project;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Schema;

class DemoContentSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::query()->where('email', 'admin@example.com')->first();

        if (! $user) {
            return;
        }

        /** @var Collection<int, PostCategory> $categories */
        $categories = PostCategory::query()->get();
        /** @var Collection<int, PostTag> $tags */
        $tags = PostTag::query()->get();

        if ($categories->isEmpty() || $tags->isEmpty()) {
            return;
        }

        if (Post::query()->count() === 0) {
            Post::factory()
                ->for($user)
                ->count(8)
                ->create()
                ->each(function (Post $post) use ($categories, $tags): void {
                    $post->category()->associate($categories->random());
                    $post->save();

                    $post->tags()->sync($tags->random(rand(2, 5))->pluck('id')->all());
                });

            Post::factory()
                ->for($user)
                ->featured()
                ->count(2)
                ->create()
                ->each(function (Post $post) use ($categories, $tags): void {
                    $post->category()->associate($categories->random());
                    $post->save();

                    $post->tags()->sync($tags->random(rand(2, 5))->pluck('id')->all());
                });
        }

        if (Project::query()->count() === 0) {
            Project::factory()
                ->for($user)
                ->count(9)
                ->create();

            Project::factory()
                ->for($user)
                ->featured()
                ->count(3)
                ->create();
        }

        if (Activity::query()->count() === 0) {
            Activity::factory()
                ->for($user)
                ->count(40)
                ->create();
        }

        $hasStartedOn = Schema::hasColumn('experiences', 'started_on');

        if ($hasStartedOn && Experience::query()->count() === 0) {
            Experience::factory()
                ->for($user)
                ->count(4)
                ->create();
        }
    }
}
