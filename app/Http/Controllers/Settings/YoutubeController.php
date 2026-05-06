<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use App\Models\Video;
use App\Models\VideoCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Inertia\Inertia;

class YoutubeController extends Controller
{
    public function index()
    {
        $videos = Video::where('user_id', auth()->id())->with('category')->get();
        $categories = VideoCategory::orderBy('name')->get();

        return Inertia::render('settings/Youtube', [
            'videos' => $videos,
            'categories' => $categories,
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'url' => 'required|url',
            'video_category_id' => 'required|exists:video_categories,id',
        ]);

        $url = $request->input('url');
        $categoryId = $request->input('video_category_id');

        // Extract video ID
        $videoId = null;
        if (preg_match('/(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?)\/|.*[?&]v=)|youtu\.be\/)([^"&?\/\s]{11})/i', $url, $match)) {
            $videoId = $match[1];
        }

        if (!$videoId) {
            return back()->withErrors(['url' => 'Could not extract a valid YouTube video ID from the URL.']);
        }

        $placeholderUrl = "https://img.youtube.com/vi/{$videoId}/hqdefault.jpg";

        // Extract title
        $title = 'Unknown Title';
        try {
            $response = Http::timeout(5)->get($url);
            $html = $response->body();
            if ($response->successful() && preg_match('/<title>(.*?)<\/title>/is', $html, $matches)) {
                $title = html_entity_decode(trim(str_replace(' - YouTube', '', $matches[1])));
            }
        } catch (\Exception $e) {
            // title defaults to Unknown Title
        }

        Video::create([
            'user_id' => auth()->id(),
            'video_category_id' => $categoryId,
            'title' => $title,
            'url' => "https://www.youtube.com/watch?v={$videoId}",
            'video_id' => $videoId,
            'placeholder_url' => $placeholderUrl,
        ]);

        return back()->with('status', 'youtube-added');
    }

    public function destroy(Video $video)
    {
        abort_if($video->user_id !== auth()->id(), 403);

        $video->delete();

        return back()->with('status', 'youtube-deleted');
    }
}
