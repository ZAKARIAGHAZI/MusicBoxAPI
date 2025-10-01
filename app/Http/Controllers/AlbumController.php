<?php

namespace App\Http\Controllers;

use App\Models\Album;
use Illuminate\Http\Request;

class AlbumController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/albums",
     *     summary="Get list of albums with pagination",
     *     tags={"Albums"},
     *     @OA\Parameter(
     *         name="page",
     *         in="query",
     *         description="Page number for pagination",
     *         required=false,
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="List of albums",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="data", type="array", @OA\Items(ref="#/components/schemas/Album")),
     *             @OA\Property(property="links", type="object"),
     *             @OA\Property(property="meta", type="object")
     *         )
     *     )
     * )
     */
    public function index()
    {
        return Album::with('artist')->paginate(10);
    }

    /**
     * @OA\Post(
     *     path="/api/albums",
     *     summary="Create a new album",
     *     tags={"Albums"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"title","artist_id"},
     *             @OA\Property(property="title", type="string", example="Discovery"),
     *             @OA\Property(property="year", type="integer", example=2001),
     *             @OA\Property(property="artist_id", type="integer", example=1)
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Album created successfully",
     *         @OA\JsonContent(ref="#/components/schemas/Album")
     *     ),
     *     @OA\Response(response=422, description="Validation error")
     * )
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string',
            'year' => 'nullable|integer',
            'artist_id' => 'required|exists:artists,id',
        ]);

        return Album::create($validated);
    }

    /**
     * @OA\Get(
     *     path="/api/albums/{id}",
     *     summary="Get a single album with its songs",
     *     tags={"Albums"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the album",
     *         @OA\Schema(type="integer", example=10)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Album found",
     *         @OA\JsonContent(ref="#/components/schemas/Album")
     *     ),
     *     @OA\Response(response=404, description="Album not found")
     * )
     */
    public function show($id)
    {
        return Album::with('songs')->findOrFail($id);
    }

    /**
     * @OA\Put(
     *     path="/api/albums/{id}",
     *     summary="Update an album",
     *     tags={"Albums"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the album to update",
     *         @OA\Schema(type="integer", example=10)
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="title", type="string", example="Random Access Memories"),
     *             @OA\Property(property="year", type="integer", example=2013),
     *             @OA\Property(property="artist_id", type="integer", example=1)
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Album updated successfully",
     *         @OA\JsonContent(ref="#/components/schemas/Album")
     *     ),
     *     @OA\Response(response=404, description="Album not found")
     * )
     */
    public function update(Request $request, $id)
    {
        $album = Album::findOrFail($id);
        $album->update($request->all());
        return $album;
    }

    /**
     * @OA\Delete(
     *     path="/api/albums/{id}",
     *     summary="Delete an album",
     *     tags={"Albums"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the album to delete",
     *         @OA\Schema(type="integer", example=10)
     *     ),
     *     @OA\Response(response=200, description="Album deleted successfully"),
     *     @OA\Response(response=404, description="Album not found")
     * )
     */
    public function destroy($id)
    {
        Album::destroy($id);
        return response()->json(['message' => 'Album deleted']);
    }
}
