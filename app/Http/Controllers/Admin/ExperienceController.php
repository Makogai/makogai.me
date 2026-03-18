<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreExperienceRequest;
use App\Http\Requests\Admin\UpdateExperienceRequest;
use App\Models\Experience;
use App\Services\Media\MediaLibrary;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class ExperienceController extends Controller
{
    public function __construct(
        protected MediaLibrary $library,
    ) {}

    /**
     * Display a listing of the resource.
     */
    public function index(): Response
    {
        $experiences = Experience::query()
            ->orderByDesc('is_current')
            ->orderBy('sort_order')
            ->orderByDesc('started_on')
            ->paginate(20)
            ->withQueryString();

        return Inertia::render('admin/experience/Index', [
            'experiences' => $experiences->through(fn (Experience $e) => [
                'id' => $e->id,
                'company' => $e->company,
                'role' => $e->role,
                'started_on' => $e->started_on?->toDateString(),
                'ended_on' => $e->ended_on?->toDateString(),
                'is_current' => $e->is_current,
                'is_featured' => $e->is_featured,
            ]),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): Response
    {
        return Inertia::render('admin/experience/Edit', [
            'experience' => null,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreExperienceRequest $request): RedirectResponse
    {
        $logoPath = $request->file('company_logo')
            ? $this->library->storeImage(
                $request->file('company_logo'),
                userId: $request->user()?->getKey(),
                collection: 'experience',
            )->path
            : null;

        $experience = Experience::query()->create([
            'user_id' => $request->user()?->getKey(),
            ...$request->safe()->except(['company_logo']),
            'company_logo_path' => $logoPath,
        ]);

        return to_route('admin.experience.edit', $experience);
    }

    /**
     * Display the specified resource.
     */
    public function show(Experience $experience): RedirectResponse
    {
        return to_route('admin.experience.edit', $experience);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Experience $experience): Response
    {
        return Inertia::render('admin/experience/Edit', [
            'experience' => $experience,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateExperienceRequest $request, Experience $experience): RedirectResponse
    {
        if ($request->file('company_logo')) {
            $experience->company_logo_path = $this->library->storeImage(
                $request->file('company_logo'),
                userId: $request->user()?->getKey(),
                collection: 'experience',
            )->path;
        }

        $experience->fill($request->safe()->except(['company_logo']));
        $experience->save();

        return back();
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Experience $experience): RedirectResponse
    {
        $experience->delete();

        return to_route('admin.experience.index');
    }
}
