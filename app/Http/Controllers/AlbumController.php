<?php

namespace App\Http\Controllers;

use App\Models\Album;
use Illuminate\Http\Request;

class AlbumController extends Controller
{
    public function index()
    {
        return Album::with('artist')->paginate(10);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string',
            'year' => 'nullable|integer',
            'artist_id' => 'required|exists:artists,id',
        ]);

        return Album::create($validated);
    }

    public function show($id)
    {
        return Album::with('songs')->findOrFail($id);
    }

    public function update(Request $request, $id)
    {
        $album = Album::findOrFail($id);
        $album->update($request->all());
        return $album;
    }

    public function destroy($id)
    {
        Album::destroy($id);
        return response()->json(['message' => 'Album deleted']);
    }
}
