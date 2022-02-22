<?php

namespace App\Models;

use App\Models\Uuid\UuidModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Project extends UuidModel
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
    ];

    /**
     * The relationship for task entities under a
     * project
     *
     * @return HasMany
     */
    public function tasks() : HasMany
    {
        return $this->hasMany(Task::class);
    }

    /**
     * The relationship for user entities under a
     * project
     *
     * @return BelongsToMany
     */
    public function users() : BelongsToMany
    {
        return $this->belongsToMany(User::class);
    }
}
