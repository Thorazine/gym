<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Exercise extends Model
{
    use HasFactory;

    protected $fillable = ['title'];

    public function bodyParts(): BelongsToMany
    {
        return $this->belongsToMany(BodyPart::class);
    }

    public function workoutItems(): BelongsToMany
    {
        return $this->belongsToMany(WorkoutItem::class);
    }
}
