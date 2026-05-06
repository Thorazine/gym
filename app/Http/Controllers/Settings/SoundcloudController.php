<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use App\Models\Soundcloud;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Inertia\Inertia;

class SoundcloudController extends Controller
{
    public function index()
    {
        $tracks = Soundcloud::all();

        return Inertia::render('settings/Soundcloud', [
            'tracks' => $tracks,
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'url' => 'required|url',
        ]);

        $url = $request->input('url');

        $response = Http::get('https://soundcloud.com/oembed', [
            'format' => 'json',
            'url' => $url,
        ]);

        if ($response->successful()) {
            $data = $response->json();
            $title = $data['title'] ?? 'Unknown Title';
            
            Soundcloud::create([
                'title' => $title,
                'url' => $url,
            ]);

            return back()->with('status', 'soundcloud-added');
        } else {
            return back()->withErrors(['url' => 'Failed to extract information from the provided Soundcloud URL.']);
        }
    }

    public function destroy(Soundcloud $soundcloud)
    {
        $soundcloud->delete();

        return back()->with('status', 'soundcloud-deleted');
    }
}
