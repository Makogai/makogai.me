<?php

namespace App\Http\Controllers\Site;

use App\Http\Controllers\Controller;
use App\Models\Activity;
use Carbon\CarbonImmutable;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ActivityController extends Controller
{
    public function index(Request $request): Response
    {
        $end = CarbonImmutable::now()->startOfDay();
        $start = $end->subDays(140);

        $activities = Activity::query()
            ->whereBetween('happened_at', [$start->toDateString(), $end->toDateString()])
            ->orderByDesc('happened_at')
            ->get([
                'id',
                'type',
                'title',
                'description',
                'happened_at',
                'url',
            ]);

        $byDate = $activities
            ->groupBy(fn (Activity $a) => $a->happened_at->toDateString())
            ->map(fn ($items) => $items->values())
            ->all();

        $counts = collect($byDate)
            ->map(fn ($items) => count($items))
            ->all();

        return Inertia::render('activity/Index', [
            'range' => [
                'start' => $start->toDateString(),
                'end' => $end->toDateString(),
            ],
            'byDate' => $byDate,
            'counts' => $counts,
        ]);
    }
}
