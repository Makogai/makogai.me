<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreActivityRequest;
use App\Http\Requests\Admin\UpdateActivityRequest;
use App\Models\Activity;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Inertia\Inertia;
use Inertia\Response;

class ActivityController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): Response
    {
        $activities = Activity::query()
            ->latest('happened_at')
            ->paginate(30, [
                'id',
                'type',
                'title',
                'happened_at',
                'url',
                'updated_at',
            ])
            ->withQueryString();

        return Inertia::render('admin/activities/Index', [
            'activities' => $activities,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): Response
    {
        return Inertia::render('admin/activities/Edit', [
            'activity' => null,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreActivityRequest $request): RedirectResponse
    {
        $activity = Activity::query()->create([
            'user_id' => $request->user()?->getKey(),
            ...$request->validated(),
        ]);

        return to_route('admin.activities.edit', $activity);
    }

    /**
     * Display the specified resource.
     */
    public function show(Activity $activity): RedirectResponse
    {
        return to_route('admin.activities.edit', $activity);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Activity $activity): Response
    {
        return Inertia::render('admin/activities/Edit', [
            'activity' => $activity->only([
                'id',
                'type',
                'title',
                'description',
                'happened_at',
                'url',
                'meta',
            ]),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateActivityRequest $request, Activity $activity): RedirectResponse
    {
        $activity->fill($request->validated());
        $activity->save();

        return back();
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Activity $activity): RedirectResponse
    {
        $activity->delete();

        return to_route('admin.activities.index');
    }

    public function syncGithub(Request $request): RedirectResponse
    {
        Artisan::call('activity:sync-github', [
            '--limit' => 60,
        ]);

        return back()->with('success', 'GitHub activity synced successfully.');
    }
}
