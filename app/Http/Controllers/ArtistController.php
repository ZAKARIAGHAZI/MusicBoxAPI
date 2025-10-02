<?php

namespace App\Http\Controllers;

use App\Models\Artist;
use Illuminate\Http\Request;

class ArtistController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/artists",
     *     summary="Get list of artists with pagination and optional genre filter",
     *     tags={"Artists"},
     *     @OA\Parameter(
     *         name="genre",
     *         in="query",
     *         description="Filter artists by genre (optional)",
     *         required=false,
     *         @OA\Schema(type="string", example="Rock")
     *     ),
     *     @OA\Parameter(
     *         name="page",
     *         in="query",
     *         description="Page number for pagination",
     *         required=false,
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful response",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="data", type="array", @OA\Items(ref="#/components/schemas/Artist")),
     *             @OA\Property(property="links", type="object"),
     *             @OA\Property(property="meta", type="object")
     *         )
     *     )
     * )
     */
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

    /**
     * @OA\Post(
     *     path="/api/artists",
     *     summary="Create a new artist",
     *     tags={"Artists"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name"},
     *             @OA\Property(property="name", type="string", example="Daft Punk"),
     *             @OA\Property(property="genre", type="string", example="Electronic"),
     *             @OA\Property(property="country", type="string", example="France")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Artist created successfully",
     *         @OA\JsonContent(ref="#/components/schemas/Artist")
     *     ),
     *     @OA\Response(
     *         response=409,
     *         description="Artist already exists",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Artist already exists"),
     *             @OA\Property(
     *                 property="artist",
     *                 ref="#/components/schemas/Artist"
     *             )
     *         )
     *     ),
     *     @OA\Response(response=422, description="Validation error")
     * )
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'    => 'required|string',
            'genre'   => 'nullable|string',
            'country' => 'nullable|string',
        ]);

        $existingArtist = Artist::where('name', $validated['name'])->first();

        if ($existingArtist) {
            return response()->json([
                'message' => 'Artist already exists',
                'artist'  => $existingArtist
            ], 409); // Conflict
        }

        return Artist::create($validated);
    }

    /**
     * @OA\Get(
     *     path="/api/artists/{id}",
     *     summary="Get a single artist with albums and songs",
     *     tags={"Artists"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the artist",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful response",
     *         @OA\JsonContent(ref="#/components/schemas/Artist")
     *     ),
     *     @OA\Response(response=404, description="Artist not found")
     * )
     */
    public function show($id)
    {
        return Artist::with('albums.songs')->findOrFail($id);
    }

    /**
     * @OA\Put(
     *     path="/api/artists/{id}",
     *     summary="Update an existing artist",
     *     tags={"Artists"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the artist to update",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="name", type="string", example="Coldplay"),
     *             @OA\Property(property="genre", type="string", example="Pop Rock"),
     *             @OA\Property(property="country", type="string", example="UK")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Artist updated successfully",
     *         @OA\JsonContent(ref="#/components/schemas/Artist")
     *     ),
     *     @OA\Response(response=404, description="Artist not found")
     * )
     */
    public function update(Request $request, $id)
    {
        $artist = Artist::findOrFail($id);
        $artist->update($request->all());
        return $artist;
    }

    /**
     * @OA\Delete(
     *     path="/api/artists/{id}",
     *     summary="Delete an artist",
     *     tags={"Artists"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the artist to delete",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(response=200, description="Artist deleted successfully"),
     *     @OA\Response(response=404, description="Artist not found")
     * )
     */
    public function destroy($id)
    {
        Artist::destroy($id);
        return response()->json(['message' => 'Artist deleted']);
    }


    /**
     * @OA\Get(
     *     path="/api/artists/search/name",
     *     summary="Search artists by name",
     *     tags={"Artists"},
     *     @OA\Parameter(
     *         name="name",
     *         in="query",
     *         description="Search term for artist name",
     *         required=true,
     *         @OA\Schema(type="string", example="Coldplay")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful response",
     *         @OA\JsonContent(type="array", @OA\Items(ref="#/components/schemas/Artist"))
     *     )
     * )
     */
    public function searchByName(Request $request)
    {
        $name = $request->input('name');

        $artists = Artist::with('albums')
            ->where('name', 'like', "%{$name}%")
            ->get();

        return response()->json($artists);
    }

    /**
     * @OA\Get(
     *     path="/api/artists/search/genre",
     *     summary="Search artists by genre",
     *     tags={"Artists"},
     *     @OA\Parameter(
     *         name="genre",
     *         in="query",
     *         description="Search term for artist genre",
     *         required=true,
     *         @OA\Schema(type="string", example="Rock")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful response",
     *         @OA\JsonContent(type="array", @OA\Items(ref="#/components/schemas/Artist"))
     *     )
     * )
     */
    public function searchByGenre(Request $request)
    {
        $genre = $request->input('genre');

        $artists = Artist::with('albums')
            ->where('genre', 'like', "%{$genre}%")
            ->get();

        return response()->json($artists);
    }
}
