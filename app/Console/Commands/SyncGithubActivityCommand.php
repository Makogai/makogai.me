<?php

namespace App\Console\Commands;

use App\Models\Activity;
use App\Models\Setting;
use Carbon\CarbonImmutable;
use Illuminate\Console\Attributes\AsCommand;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

#[AsCommand(name: 'activity:sync-github')]
#[Signature('activity:sync-github {--limit=60}')]
#[Description('Sync public GitHub events into activity feed')]
class SyncGithubActivityCommand extends Command
{
    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        /** @var array<string, mixed> $site */
        $site = Setting::query()->where('key', 'site')->value('value') ?? [];

        $username = (string) ($site['github_activity_username'] ?? env('GITHUB_ACTIVITY_USERNAME', ''));
        $token = (string) env('GITHUB_ACTIVITY_TOKEN', '');

        if ($username === '') {
            $this->warn('Missing github_activity_username in settings or GITHUB_ACTIVITY_USERNAME env.');

            return self::FAILURE;
        }

        $obfuscate = (bool) ($site['github_activity_obfuscate'] ?? true);
        $delayHours = (int) ($site['github_activity_delay_hours'] ?? 0);
        $limit = max(1, min(100, (int) $this->option('limit')));

        $headers = [
            'Accept' => 'application/vnd.github+json',
            'X-GitHub-Api-Version' => '2022-11-28',
            'User-Agent' => config('app.name', 'makogai').'-activity-sync',
        ];
        if ($token !== '') {
            $headers['Authorization'] = 'Bearer '.$token;
        }

        $response = Http::timeout(20)
            ->withHeaders($headers)
            ->get("https://api.github.com/users/{$username}/events/public?per_page={$limit}");

        if (! $response->ok()) {
            $this->error("GitHub API failed: {$response->status()}");

            return self::FAILURE;
        }

        $events = $response->json();
        if (! is_array($events)) {
            $this->error('Unexpected GitHub response payload.');

            return self::FAILURE;
        }

        $synced = 0;
        foreach ($events as $event) {
            if (! is_array($event)) {
                continue;
            }

            $normalized = $this->normalizeEvent($event, $obfuscate, $delayHours);
            if (! $normalized) {
                continue;
            }

            Activity::query()->updateOrCreate(
                [
                    'source' => 'github',
                    'external_id' => $normalized['external_id'],
                ],
                [
                    'type' => $normalized['type'],
                    'title' => $normalized['title'],
                    'description' => $normalized['description'],
                    'happened_at' => $normalized['happened_at'],
                    'url' => $normalized['url'],
                    'meta' => $normalized['meta'],
                ],
            );
            $synced++;
        }

        $this->info("Synced {$synced} GitHub activities for {$username}.");

        return self::SUCCESS;
    }

    /**
     * @param  array<string, mixed>  $event
     * @return array<string, mixed>|null
     */
    protected function normalizeEvent(array $event, bool $obfuscate, int $delayHours): ?array
    {
        $externalId = (string) ($event['id'] ?? '');
        $type = (string) ($event['type'] ?? '');
        $repoName = (string) (($event['repo']['name'] ?? '') ?: 'repository');
        $createdAt = (string) ($event['created_at'] ?? '');
        $url = (string) ($event['repo']['url'] ?? '');

        if ($externalId === '' || $createdAt === '') {
            return null;
        }

        try {
            $happened = CarbonImmutable::parse($createdAt);
        } catch (\Throwable) {
            return null;
        }

        if ($delayHours > 0) {
            $visibleAfter = $happened->addHours($delayHours);
            if ($visibleAfter->isFuture()) {
                return null;
            }
        }

        $map = [
            'PushEvent' => ['type' => 'feature', 'title' => 'Pushed commits'],
            'PullRequestEvent' => ['type' => 'feature', 'title' => 'Updated pull request'],
            'IssuesEvent' => ['type' => 'idea', 'title' => 'Updated issue'],
            'IssueCommentEvent' => ['type' => 'idea', 'title' => 'Commented on issue'],
            'CreateEvent' => ['type' => 'feature', 'title' => 'Created repository item'],
            'DeleteEvent' => ['type' => 'fix', 'title' => 'Removed repository item'],
            'ReleaseEvent' => ['type' => 'feature', 'title' => 'Published release'],
            'ForkEvent' => ['type' => 'feature', 'title' => 'Forked repository'],
            'WatchEvent' => ['type' => 'idea', 'title' => 'Starred repository'],
        ];

        $mapped = $map[$type] ?? ['type' => 'feature', 'title' => 'GitHub activity'];

        $safeRepo = $obfuscate ? 'a private project' : $repoName;
        $title = $mapped['title'];
        $description = "Activity from {$safeRepo}.";

        if (! $obfuscate && $repoName !== '') {
            $title = "{$mapped['title']} in {$repoName}";
            $description = "GitHub {$type} event in {$repoName}.";
        }

        return [
            'external_id' => $externalId,
            'type' => $mapped['type'],
            'title' => Str::limit($title, 120),
            'description' => Str::limit($description, 220),
            'happened_at' => $happened->toDateString(),
            'url' => $obfuscate ? null : $this->toGithubRepoUrl($url, $repoName),
            'meta' => [
                'event_type' => $type,
                'repo' => $obfuscate ? null : $repoName,
                'obfuscated' => $obfuscate,
            ],
        ];
    }

    protected function toGithubRepoUrl(string $apiRepoUrl, string $repoName): ?string
    {
        if ($repoName !== '') {
            return "https://github.com/{$repoName}";
        }

        if ($apiRepoUrl === '') {
            return null;
        }

        return preg_replace('#^https://api\.github\.com/repos/#', 'https://github.com/', $apiRepoUrl) ?: null;
    }
}
