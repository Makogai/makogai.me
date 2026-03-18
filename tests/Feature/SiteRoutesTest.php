<?php

use App\Models\Post;
use App\Models\Project;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('renders the main public pages', function () {
    $this->get('/')->assertSuccessful();
    $this->get('/about')->assertSuccessful();
    $this->get('/projects')->assertSuccessful();
    $this->get('/blog')->assertSuccessful();
    $this->get('/activity')->assertSuccessful();
});

it('renders a published blog post page', function () {
    $post = Post::factory()->create([
        'published_at' => now()->subDay(),
    ]);

    $this->get("/blog/{$post->slug}")->assertSuccessful();
});

it('renders a published project page', function () {
    $project = Project::factory()->create([
        'published_at' => now()->subDay(),
    ]);

    $this->get("/projects/{$project->slug}")->assertSuccessful();
});
