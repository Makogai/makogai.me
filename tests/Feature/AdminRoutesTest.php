<?php

use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('protects admin routes behind authentication', function () {
    $this->get('/admin')->assertRedirect('/login');
    $this->get('/admin/posts')->assertRedirect('/login');
});

it('allows authenticated users to access admin', function () {
    $user = User::factory()->create([
        'email_verified_at' => now(),
    ]);

    $this->actingAs($user);

    $this->get('/admin')->assertSuccessful();
    $this->get('/admin/posts')->assertSuccessful();
    $this->get('/admin/projects')->assertSuccessful();
    $this->get('/admin/activities')->assertSuccessful();
});

it('binds admin post routes by id', function () {
    $user = User::factory()->create([
        'email_verified_at' => now(),
    ]);

    $post = Post::factory()->create();

    $this->actingAs($user);

    $this->get("/admin/posts/{$post->id}/edit")->assertSuccessful();
});
