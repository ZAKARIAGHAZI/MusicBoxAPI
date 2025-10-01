<?php

namespace App\Swagger\Schemas;

/**
 * @OA\Schema(
 *     schema="Album",
 *     type="object",
 *     title="Album",
 *     description="Album model",
 *     @OA\Property(property="id", type="integer", example=10),
 *     @OA\Property(property="title", type="string", example="Discovery"),
 *     @OA\Property(property="year", type="integer", example=2001),
 *     @OA\Property(property="artist_id", type="integer", example=1),
 *     @OA\Property(
 *         property="songs",
 *         type="array",
 *         description="Songs in the album",
 *         @OA\Items(ref="#/components/schemas/Song")
 *     )
 * )
 */
class AlbumSchema {}
