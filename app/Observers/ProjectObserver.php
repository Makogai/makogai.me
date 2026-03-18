<?php

namespace App\Observers;

use App\Models\Project;
use App\Services\Content\MarkdownService;
use Illuminate\Support\Str;

class ProjectObserver
{
    public function __construct(
        protected MarkdownService $markdown,
    ) {}

    public function saving(Project $project): void
    {
        if (! $project->slug) {
            $project->slug = $this->uniqueSlug($project, (string) $project->title);
        }

        if ($project->isDirty('slug')) {
            $project->slug = $this->uniqueSlug($project, (string) $project->slug);
        }

        if ($project->isDirty('title') && ! $project->isDirty('slug')) {
            $project->slug = $this->uniqueSlug($project, (string) $project->title);
        }

        if (
            $project->isDirty('content_markdown')
            && $project->content_markdown !== null
        ) {
            $project->content_html = $this->markdown->renderToHtml((string) $project->content_markdown);
        }
    }

    protected function uniqueSlug(Project $project, string $value): string
    {
        $base = Str::slug($value) ?: 'project';
        $slug = $base;
        $suffix = 2;

        while (
            Project::query()
                ->where('slug', $slug)
                ->when($project->exists, fn ($q) => $q->whereKeyNot($project->getKey()))
                ->exists()
        ) {
            $slug = "{$base}-{$suffix}";
            $suffix++;
        }

        return $slug;
    }
}
