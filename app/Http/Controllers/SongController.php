<?php

namespace App\Http\Controllers;

use App\Models\Song;
use Illuminate\Http\Request;

class SongController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/songs",
     *     summary="Get list of songs with pagination",
     *     tags={"Songs"},
     *     @OA\Parameter(
     *         name="page",
     *         in="query",
     *         description="Page number for pagination",
     *         required=false,
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="List of songs",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="data", type="array", @OA\Items(ref="#/components/schemas/Song")),
     *             @OA\Property(property="links", type="object"),
     *             @OA\Property(property="meta", type="object")
     *         )
     *     )
     * )
     */
    public function index()
    {
        return Song::with('album.artist')->paginate(10);
    }

    /**
     * @OA\Post(
     *     path="/api/songs",
     *     summary="Create a new song",
     *     tags={"Songs"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"title","album_id"},
     *             @OA\Property(property="title", type="string", example="Harder, Better, Faster, Stronger"),
     *             @OA\Property(property="duration", type="integer", example=225),
     *             @OA\Property(property="album_id", type="integer", example=10)
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Song created successfully",
     *         @OA\JsonContent(ref="#/components/schemas/Song")
     *     ),
     *     @OA\Response(
     *         response=409,
     *         description="Song already exists in this album",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Song already exists in this album"),
     *             @OA\Property(property="song", ref="#/components/schemas/Song")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error"
     *     )
     * )
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title'     => 'required|string|max:255',
            'duration'  => 'nullable|integer',
            'album_id'  => 'required|exists:albums,id',
        ]);

        // Check if song already exists in this album
        $existingSong = Song::where('title', $validated['title'])
            ->where('album_id', $validated['album_id'])
            ->first();

        if ($existingSong) {
            return response()->json([
                'message' => 'Song already exists in this album',
                'song'    => $existingSong
            ], 409); // Conflict
        }

        $song = Song::create($validated);

        return response()->json([
            'message' => 'Song created successfully',
            'data'    => $song
        ], 201);
    }


    /**
     * @OA\Get(
     *     path="/api/songs/{id}",
     *     summary="Get a single song with album and artist",
     *     tags={"Songs"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the song",
     *         @OA\Schema(type="integer", example=100)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Song found",
     *         @OA\JsonContent(ref="#/components/schemas/Song")
     *     ),
     *     @OA\Response(response=404, description="Song not found")
     * )
     */
    public function show($id)
    {
        return Song::with('album.artist')->findOrFail($id);
    }

    /**
     * @OA\Put(
     *     path="/api/songs/{id}",
     *     summary="Update a song",
     *     tags={"Songs"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the song to update",
     *         @OA\Schema(type="integer", example=100)
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="title", type="string", example="One More Time"),
     *             @OA\Property(property="duration", type="integer", example=320),
     *             @OA\Property(property="album_id", type="integer", example=10)
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Song updated successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Song updated successfully"),
     *             @OA\Property(property="data", ref="#/components/schemas/Song")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Song not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Song not found")
     *         )
     *     )
     * )
     */
    public function update(Request $request, $id)
    {
        $song = Song::findOrFail($id);
        $validated = $request->validate([
            'title'     => 'sometimes|required|string|max:255',
            'duration'  => 'nullable|integer',
            'album_id'  => 'sometimes|required|exists:albums,id',
        ]);
        $song->update($validated);

        return response()->json([
            'message' => 'Song updated successfully',
            'data'    => $song
        ], 200);
    }

    /**
     * @OA\Delete(
     *     path="/api/songs/{id}",
     *     summary="Delete a song",
     *     tags={"Songs"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the song to delete",
     *         @OA\Schema(type="integer", example=100)
     *     ),
     *     @OA\Response(response=200, description="Song deleted successfully"),
     *     @OA\Response(response=404, description="Song not found")
     * )
     */
    public function destroy($id)
    {
        Song::destroy($id);
        return response()->json(['message' => 'Song deleted']);
    }

    /**
     * @OA\Get(
     *     path="/api/songs/search",
     *     summary="Search songs by title or artist name",
     *     tags={"Songs"},
     *     @OA\Parameter(
     *         name="q",
     *         in="query",
     *         description="Search query (title or artist name)",
     *         required=true,
     *         @OA\Schema(type="string", example="Daft Punk")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Search results",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="data", type="array", @OA\Items(ref="#/components/schemas/Song")),
     *             @OA\Property(property="links", type="object"),
     *             @OA\Property(property="meta", type="object")
     *         )
     *     )
     * )
     */
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
