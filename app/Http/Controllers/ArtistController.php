<?php

namespace App\Http\Controllers;

use App\Models\Artist;
use Illuminate\Http\Request;

class ArtistController extends Controller
{
    public function index(Request $request)
    {
        $genre = $request->input('genre');

        $artists = Artist::with('albums')
            ->when($genre, function ($query) use ($genre) {
                $query->where('genre', 'like', "%{$genre}%");
            })
            ->paginate(10);

        return $artists;
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string',
            'genre' => 'nullable|string',
            'country' => 'nullable|string',
        ]);

        return Artist::create($validated);
    }

    public function show($id)
    {
        return Artist::with('albums.songs')->findOrFail($id);
    }

    public function update(Request $request, $id)
    {
        $artist = Artist::findOrFail($id);
        $artist->update($request->all());
        return $artist;
    }

    public function destroy($id)
    {
        Artist::destroy($id);
        return response()->json(['message' => 'Artist deleted']);
    }
}
