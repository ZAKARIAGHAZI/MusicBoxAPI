<?php

namespace App\Swagger\Schemas;

/**
 * @OA\Schema(
 *     schema="Artist",
 *     type="object",
 *     title="Artist",
 *     description="Artist model",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="name", type="string", example="Daft Punk"),
 *     @OA\Property(property="genre", type="string", example="Electronic"),
 *     @OA\Property(property="country", type="string", example="France"),
 *     @OA\Property(property="created_at", type="string", format="date-time", example="2025-09-30T12:00:00Z"),
 *     @OA\Property(property="updated_at", type="string", format="date-time", example="2025-09-30T12:30:00Z"),
 *     @OA\Property(
 *         property="albums",
 *         type="array",
 *         description="List of albums for the artist",
 *         @OA\Items(ref="#/components/schemas/Album")
 *     )
 * )
 */
class ArtistSchema {}
