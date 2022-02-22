<?php

namespace App\Models;

use App\Models\Enums\TaskStatus;
use App\Models\Uuid\UuidModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Task extends UuidModel
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'title',
        'description',
        'status',
        'user_id',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'status' => TaskStatus::class,
    ];


    /**
     * The relationship for the parent project entity
     * for a task
     *
     * @return BelongsTo
     */
    public function project() : BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    /**
     * The relationship for the associated user entity
     * for a task
     *
     * @return BelongsTo
     */
    public function user() : BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
