<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreProjectRequest;
use App\Http\Requests\Admin\UpdateProjectRequest;
use App\Models\Project;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class ProjectController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): Response
    {
        $projects = Project::query()
            ->latest('updated_at')
            ->paginate(20, [
                'id',
                'title',
                'slug',
                'published_at',
                'is_featured',
                'updated_at',
            ])
            ->withQueryString();

        return Inertia::render('admin/projects/Index', [
            'projects' => $projects,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): Response
    {
        return Inertia::render('admin/projects/Edit', [
            'project' => null,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreProjectRequest $request): RedirectResponse
    {
        $project = Project::query()->create([
            'user_id' => $request->user()?->getKey(),
            ...$request->validated(),
        ]);

        return to_route('admin.projects.edit', $project);
    }

    /**
     * Display the specified resource.
     */
    public function show(Project $project): RedirectResponse
    {
        return to_route('admin.projects.edit', $project);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Project $project): Response
    {
        return Inertia::render('admin/projects/Edit', [
            'project' => $project->only([
                'id',
                'title',
                'slug',
                'description',
                'content_markdown',
                'tech_stack',
                'repo_url',
                'demo_url',
                'published_at',
                'is_featured',
                'seo_title',
                'seo_description',
            ]),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateProjectRequest $request, Project $project): RedirectResponse
    {
        $project->fill($request->validated());
        $project->save();

        return back();
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Project $project): RedirectResponse
    {
        $project->delete();

        return to_route('admin.projects.index');
    }
}
