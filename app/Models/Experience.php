<?php

namespace App\Models;

use Database\Factories\ExperienceFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Experience extends Model
{
    /** @use HasFactory<ExperienceFactory> */
    use HasFactory;

    protected $fillable = [
        'user_id',
        'company',
        'company_logo_path',
        'role',
        'location',
        'employment_type',
        'summary',
        'highlights',
        'started_on',
        'ended_on',
        'is_current',
        'company_url',
        'sort_order',
        'is_featured',
    ];

    /**
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'highlights' => 'array',
            'started_on' => 'date',
            'ended_on' => 'date',
            'is_current' => 'bool',
            'is_featured' => 'bool',
            'sort_order' => 'int',
        ];
    }
}
