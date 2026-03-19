<?php

namespace App\Http\Controllers\Site;

use App\Http\Controllers\Controller;
use App\Models\Project;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ProjectController extends Controller
{
    public function index(Request $request): Response
    {
        $query = Project::query()
            ->published()
            ->latest('published_at');

        $search = (string) $request->query('q', '');
        $tech = (string) $request->query('tech', '');
        $featuredFirst = (string) $request->query('featured', '') === '1';

        if ($search !== '') {
            $query->where(function ($q) use ($search): void {
                $q->where('title', 'like', '%'.$search.'%')
                    ->orWhere('description', 'like', '%'.$search.'%')
                    ->orWhere('tech_stack', 'like', '%'.$search.'%');
            });
        }

        if ($tech !== '') {
            $query->whereJsonContains('tech_stack', $tech);
        }

        if ($featuredFirst) {
            $query->orderByDesc('is_featured');
        }

        $projects = $query->get([
            'id',
            'title',
            'slug',
            'description',
            'tech_stack',
            'cover_image_path',
            'repo_url',
            'demo_url',
            'is_featured',
            'published_at',
        ]);

        $siteSettings = $request->attributes->get('settings.site') ?? [];

        $title = $siteSettings['default_seo_title'] ?? ($siteSettings['site_name'] ?? config('app.name'));
        $description = $siteSettings['default_seo_description'] ?? ($siteSettings['tagline'] ?? null);

        return Inertia::render('projects/Index', [
            'projects' => $projects,
            'filters' => [
                'q' => $search,
                'tech' => $tech,
                'featured' => $featuredFirst ? '1' : '0',
            ],
            'meta' => [
                'title' => $title.' – Projects',
                'description' => $description,
                'type' => 'website',
                'image_path' => $siteSettings['default_og_image_path'] ?? null,
            ],
        ]);
    }

    public function show(Project $project): Response
    {
        abort_unless($project->published_at && ! $project->archived_at, 404);

        $related = Project::query()
            ->published()
            ->where('id', '!=', $project->id)
            ->when(
                $project->tech_stack,
                fn ($q) => $q->whereJsonOverlap('tech_stack', $project->tech_stack),
            )
            ->latest('published_at')
            ->limit(4)
            ->get([
                'id',
                'title',
                'slug',
                'description',
                'tech_stack',
                'cover_image_path',
                'published_at',
            ]);

        $siteSettings = request()->attributes->get('settings.site') ?? [];

        $title = $project->seo_title ?: ($project->title.' – Project');
        $description = $project->seo_description ?: ($project->description ?: ($siteSettings['default_seo_description'] ?? null));

        return Inertia::render('projects/Show', [
            'project' => $project->only([
                'id',
                'title',
                'slug',
                'description',
                'content_html',
                'cover_image_path',
                'gallery',
                'tech_stack',
                'repo_url',
                'demo_url',
                'published_at',
                'seo_title',
                'seo_description',
            ]),
            'related' => $related,
            'meta' => [
                'title' => $title,
                'description' => $description,
                'type' => 'article',
                'image_path' => $project->cover_image_path ?: ($siteSettings['default_og_image_path'] ?? null),
            ],
        ]);
    }
}
