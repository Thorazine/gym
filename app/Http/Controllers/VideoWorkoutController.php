<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\User;
use App\Models\Video;
use App\Models\VideoCategory;
use Inertia\Inertia;

class VideoWorkoutController extends Controller
{
    public function categories(User $user)
    {
        $categories = VideoCategory::orderBy('name')->get();

        return Inertia::render('Workout/VideoCategories', [
            'user' => $user,
            'categories' => $categories,
        ]);
    }

    public function videos(User $user, VideoCategory $videoCategory)
    {
        $videos = $videoCategory->videos()->where('user_id', $user->id)->latest()->get();

        return Inertia::render('Workout/Videos', [
            'user' => $user,
            'category' => $videoCategory,
            'videos' => $videos,
        ]);
    }

    public function play(User $user, Video $video)
    {
        return Inertia::render('Workout/PlayVideo', [
            'user' => $user,
            'video' => $video,
        ]);
    }
}
