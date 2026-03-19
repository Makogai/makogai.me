<?php

use App\Http\Controllers\Admin\ActivityController as AdminActivityController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\ExperienceController as AdminExperienceController;
use App\Http\Controllers\Admin\MarkdownPreviewController;
use App\Http\Controllers\Admin\MediaController as AdminMediaController;
use App\Http\Controllers\Admin\PostController as AdminPostController;
use App\Http\Controllers\Admin\ProfileMediaController;
use App\Http\Controllers\Admin\ProjectController as AdminProjectController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\Site\AboutController;
use App\Http\Controllers\Site\ActivityController;
use App\Http\Controllers\Site\BlogController;
use App\Http\Controllers\Site\HomeController;
use App\Http\Controllers\Site\ProjectController;
use Illuminate\Support\Facades\Route;

Route::get('/', HomeController::class)->name('home');

Route::get('/projects', [ProjectController::class, 'index'])->name('projects.index');
Route::get('/projects/{project}', [ProjectController::class, 'show'])->name('projects.show');

Route::get('/about', AboutController::class)->name('about');

Route::get('/blog', [BlogController::class, 'index'])->name('blog.index');
Route::get('/blog/{post}', [BlogController::class, 'show'])->name('blog.show');

Route::get('/activity', [ActivityController::class, 'index'])->name('activity.index');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::inertia('dashboard', 'Dashboard')->name('dashboard');

    Route::prefix('admin')->name('admin.')->group(function () {
        Route::get('/', AdminDashboardController::class)->name('dashboard');

        Route::post('markdown/preview', MarkdownPreviewController::class)
            ->name('markdown.preview');

        Route::post('profile/media', ProfileMediaController::class)->name('profile.media');

        Route::get('media', [AdminMediaController::class, 'index'])->name('media.index');
        Route::post('media', [AdminMediaController::class, 'store'])->name('media.store');
        Route::get('media/library', [AdminMediaController::class, 'library'])->name('media.library');
        Route::get('media/{media}/usage', [AdminMediaController::class, 'usage'])->name('media.usage');
        Route::delete('media/{media}', [AdminMediaController::class, 'destroy'])->name('media.destroy');

        Route::resource('posts', AdminPostController::class)->scoped([
            'post' => 'id',
        ]);
        Route::resource('projects', AdminProjectController::class)->scoped([
            'project' => 'id',
        ]);
        Route::resource('activities', AdminActivityController::class);
        Route::resource('experience', AdminExperienceController::class);

        Route::get('settings', [SettingController::class, 'edit'])->name('settings.edit');
        Route::put('settings', [SettingController::class, 'update'])->name('settings.update');
        Route::put('settings/profile', [SettingController::class, 'updateProfile'])->name('settings.profile.update');
    });
});

require __DIR__.'/settings.php';
