<?php

namespace App\Http\Controllers;

use App\Models\Song;
use Illuminate\Http\Request;

class SongController extends Controller
{
    public function index()
    {
        return Song::with('album.artist')->paginate(10);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string',
            'duration' => 'nullable|integer',
            'album_id' => 'required|exists:albums,id',
        ]);

        return Song::create($validated);
    }

    public function show($id)
    {
        return Song::with('album.artist')->findOrFail($id);
    }

    public function update(Request $request, $id)
    {
        $song = Song::findOrFail($id);
        $song->update($request->all());
        return $song;
    }

    public function destroy($id)
    {
        Song::destroy($id);
        return response()->json(['message' => 'Song deleted']);
    }

    public function search(Request $request)
    {
        $query = $request->input('q');

        $songs = Song::with('album.artist')
            ->where('title', 'like', "%{$query}%")
            ->orWhereHas('album.artist', function ($q) use ($query) {
                $q->where('name', 'like', "%{$query}%");
            })
            ->paginate(10);

        return $songs;
    }
}
