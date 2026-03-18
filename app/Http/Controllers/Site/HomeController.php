<?php

namespace App\Http\Controllers\Site;

use App\Http\Controllers\Controller;
use App\Models\Activity;
use App\Models\Experience;
use App\Models\Post;
use App\Models\Project;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class HomeController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request): Response
    {
        $featuredProjects = Project::query()
            ->published()
            ->where('is_featured', true)
            ->latest('published_at')
            ->take(6)
            ->get([
                'id',
                'title',
                'slug',
                'description',
                'tech_stack',
                'cover_image_path',
                'repo_url',
                'demo_url',
                'published_at',
            ]);

        $latestPosts = Post::query()
            ->published()
            ->latest('published_at')
            ->with(['category:id,name,slug', 'tags:id,name,slug'])
            ->take(4)
            ->get([
                'id',
                'title',
                'slug',
                'excerpt',
                'reading_time_minutes',
                'cover_image_path',
                'category_id',
                'published_at',
            ]);

        // dd(Post::query()->published()->get());

        $recentActivity = Activity::query()
            ->latest('happened_at')
            ->take(12)
            ->get([
                'id',
                'type',
                'title',
                'description',
                'happened_at',
                'url',
            ]);

        $experience = Experience::query()
            ->where('is_featured', true)
            ->orderByDesc('is_current')
            ->orderBy('sort_order')
            ->orderByDesc('started_on')
            ->take(6)
            ->get([
                'id',
                'company',
                'company_logo_path',
                'role',
                'location',
                'employment_type',
                'summary',
                'highlights',
                'started_on',
                'ended_on',
                'is_current',
                'company_url',
            ]);

        return Inertia::render('site/Home', [
            'featuredProjects' => $featuredProjects,
            'latestPosts' => $latestPosts,
            'recentActivity' => $recentActivity,
            'experience' => $experience,
        ]);
    }
}
