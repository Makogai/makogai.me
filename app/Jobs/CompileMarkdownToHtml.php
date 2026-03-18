<?php

namespace App\Jobs;

use App\Models\Post;
use App\Models\Project;
use App\Services\Content\MarkdownService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\SerializesModels;

class CompileMarkdownToHtml implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(
        public string $modelClass,
        public int $modelId,
    ) {}

    public function handle(MarkdownService $markdown): void
    {
        if (! in_array($this->modelClass, [Post::class, Project::class], true)) {
            return;
        }

        /** @var Post|Project|null $model */
        $model = $this->modelClass::query()->withTrashed()->find($this->modelId);

        if (! $model) {
            return;
        }

        $markdownValue = (string) ($model->content_markdown ?? '');

        $payload = [
            'content_html' => $markdown->renderToHtml($markdownValue),
        ];

        if ($model instanceof Post) {
            $payload['excerpt'] = $markdown->excerpt($markdownValue);
            $payload['reading_time_minutes'] = $markdown->readingTimeMinutes($markdownValue);
        }

        $model->forceFill($payload)->saveQuietly();
    }
}
