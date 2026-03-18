<?php

use App\Models\Experience;
use App\Models\User;
use Illuminate\Support\Carbon;

test('admin experience update persists started_on/ended_on', function () {
    $user = User::factory()->create();
    $experience = Experience::factory()->create([
        'is_current' => false,
    ]);

    $newStarted = Carbon::parse('2026-01-15')->toDateString();
    $newEnded = Carbon::parse('2026-02-20')->toDateString();

    $this->actingAs($user)->put(
        route('admin.experience.update', $experience),
        [
            'company' => $experience->company,
            'role' => $experience->role,
            'location' => $experience->location,
            'employment_type' => $experience->employment_type,
            'summary' => $experience->summary,
            'highlights' => $experience->highlights,
            'started_on' => $newStarted,
            'ended_on' => $newEnded,
            'is_current' => false,
            'company_url' => $experience->company_url,
            'sort_order' => $experience->sort_order,
            'is_featured' => $experience->is_featured,
        ],
    );

    expect($experience->fresh()->started_on?->toDateString())->toBe($newStarted);
    expect($experience->fresh()->ended_on?->toDateString())->toBe($newEnded);
});

