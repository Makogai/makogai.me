<?php

use App\Models\Post;
use App\Models\User;

it('treats new/draft posts as not public', function () {
    $post = Post::factory()->draft()->create();

    $this->get('/blog')->assertSuccessful();
    $this->get("/blog/{$post->slug}")->assertNotFound();
});

it('shows published posts publicly', function () {
    $post = Post::factory()->create([
        'published_at' => now()->subMinute(),
        'archived_at' => null,
    ]);

    $this->get('/blog')->assertSuccessful();
    $this->get("/blog/{$post->slug}")->assertSuccessful();
});

it('hides archived posts publicly even if they were published', function () {
    $post = Post::factory()->create([
        'published_at' => now()->subDay(),
        'archived_at' => now()->subMinute(),
    ]);

    $this->get('/blog')->assertSuccessful();
    $this->get("/blog/{$post->slug}")->assertNotFound();
});

it('lets admin set status fields', function () {
    $user = User::factory()->create([
        'email_verified_at' => now(),
    ]);
    $post = Post::factory()->draft()->create();

    $this->actingAs($user)
        ->from("/admin/posts/{$post->id}/edit")
        ->put("/admin/posts/{$post->id}", [
            'title' => $post->title,
            'slug' => $post->slug,
            'excerpt' => $post->excerpt,
            'content_markdown' => $post->content_markdown,
            'category_id' => $post->category_id,
            'tag_ids' => [],
            'cover_image_path' => $post->cover_image_path,
            'is_featured' => false,
            'published_at' => now()->subMinute()->toDateTimeString(),
            'archived_at' => null,
            'seo_title' => null,
            'seo_description' => null,
        ])
        ->assertSessionHasNoErrors()
        ->assertRedirect("/admin/posts/{$post->id}/edit");

    $post->refresh();

    expect($post->published_at)->not->toBeNull();
    expect($post->archived_at)->toBeNull();
});
