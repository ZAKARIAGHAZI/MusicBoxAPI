<?php

namespace App\Swagger\Schemas;

/**
 * @OA\Schema(
 *     schema="Song",
 *     type="object",
 *     title="Song",
 *     description="Song model",
 *     @OA\Property(property="id", type="integer", example=100),
 *     @OA\Property(property="title", type="string", example="Harder, Better, Faster, Stronger"),
 *     @OA\Property(property="duration", type="string", example="03:45"),
 *     @OA\Property(property="album_id", type="integer", example=10)
 * )
 */
class SongSchema {}
