<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Video extends Model
{
    protected $fillable = [
        'video_category_id',
        'title',
        'url',
        'video_id',
        'placeholder_url',
    ];

    public function category()
    {
        return $this->belongsTo(VideoCategory::class, 'video_category_id');
    }
}
