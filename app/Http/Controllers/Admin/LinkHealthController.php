<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Post;
use App\Models\Project;
use App\Models\Setting;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Inertia\Inertia;
use Inertia\Response;

class LinkHealthController extends Controller
{
    public function __invoke(): Response
    {
        $forceRefresh = request()->boolean('refresh');
        $site = Setting::query()->where('key', 'site')->value('value') ?? [];

        $records = collect();

        foreach (['github_url', 'linkedin_url', 'x_url'] as $field) {
            $url = (string) ($site[$field] ?? '');
            if ($url !== '') {
                $records->push([
                    'url' => $url,
                    'source' => 'settings',
                    'label' => "Site {$field}",
                    'admin_url' => '/admin/settings',
                ]);
            }
        }

        Post::query()
            ->latest('id')
            ->limit(100)
            ->get(['id', 'title', 'content_markdown'])
            ->each(function (Post $post) use ($records): void {
                foreach ($this->extractUrls((string) $post->content_markdown) as $url) {
                    $records->push([
                        'url' => $url,
                        'source' => 'post',
                        'label' => $post->title,
                        'admin_url' => "/admin/posts/{$post->id}/edit",
                    ]);
                }
            });

        Project::query()
            ->latest('id')
            ->limit(100)
            ->get(['id', 'title', 'repo_url', 'demo_url', 'content_markdown'])
            ->each(function (Project $project) use ($records): void {
                foreach ([$project->repo_url, $project->demo_url] as $url) {
                    if (is_string($url) && $url !== '') {
                        $records->push([
                            'url' => $url,
                            'source' => 'project',
                            'label' => $project->title,
                            'admin_url' => "/admin/projects/{$project->id}/edit",
                        ]);
                    }
                }

                foreach ($this->extractUrls((string) $project->content_markdown) as $url) {
                    $records->push([
                        'url' => $url,
                        'source' => 'project',
                        'label' => $project->title,
                        'admin_url' => "/admin/projects/{$project->id}/edit",
                    ]);
                }
            });

        $results = $records
            ->unique(fn (array $item) => $item['url'].'|'.$item['admin_url'])
            ->map(function (array $item) use ($forceRefresh): array {
                $health = $this->checkUrl($item['url'], $forceRefresh);

                return [
                    ...$item,
                    'status_code' => $health['status_code'],
                    'ok' => $health['ok'],
                    'error' => $health['error'],
                ];
            })
            ->values();

        return Inertia::render('admin/link-health/Index', [
            'links' => $results,
            'summary' => [
                'total' => $results->count(),
                'ok' => $results->where('ok', true)->count(),
                'broken' => $results->where('ok', false)->count(),
            ],
            'refreshed' => $forceRefresh,
        ]);
    }

    /**
     * @return array{ok: bool, status_code: int|null, error: string|null}
     */
    protected function checkUrl(string $url, bool $forceRefresh = false): array
    {
        $cacheKey = 'link-health:'.sha1($url);
        if (! $forceRefresh) {
            $cached = Cache::get($cacheKey);
            if (is_array($cached)) {
                return $cached;
            }
        }

        try {
            $client = Http::timeout(8)
                ->retry(1, 200)
                ->withHeaders([
                    'User-Agent' => config('app.name', 'Laravel').' LinkHealthBot/1.0',
                ]);

            $response = $client->head($url);
            if ($response->status() === 405) {
                $response = $client->get($url);
            }

            $status = $response->status();

            $result = [
                'ok' => $status >= 200 && $status < 400,
                'status_code' => $status,
                'error' => null,
            ];
            Cache::put($cacheKey, $result, now()->addMinutes(10));

            return $result;
        } catch (ConnectionException|RequestException $e) {
            $result = [
                'ok' => false,
                'status_code' => null,
                'error' => $e->getMessage(),
            ];
            Cache::put($cacheKey, $result, now()->addMinutes(10));

            return $result;
        } catch (\Throwable $e) {
            $result = [
                'ok' => false,
                'status_code' => null,
                'error' => $e->getMessage(),
            ];
            Cache::put($cacheKey, $result, now()->addMinutes(10));

            return $result;
        }
    }

    /**
     * @return Collection<int, string>
     */
    protected function extractUrls(string $markdown): Collection
    {
        preg_match_all('/https?:\/\/[^\s\)\]">]+/i', $markdown, $matches);
        $urls = collect($matches[0] ?? [])
            ->map(fn (string $url) => rtrim($url, '.,;:'))
            ->filter(fn (string $url) => str_starts_with($url, 'http://') || str_starts_with($url, 'https://'))
            ->unique()
            ->values();

        return $urls;
    }
}
