<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

/**
 * @OA\Info(
 *     title="🎶 MusicBoxAPI",
 *     version="1.0.0",
 *     description="API RESTful pour la gestion des artistes, albums et chansons.
 *                   Cette API permet de gérer les ressources principales :
 *                   - Artistes
 *                   - Albums
 *                   - Chansons
 *                   Elle inclut des fonctionnalités de recherche, pagination et filtres.",
 *     @OA\Contact(
 *         email="support@musicboxapi.com",
 *         name="MusicBox API Team"
 *     ),
 *     @OA\License(
 *         name="MIT",
 *         url="https://opensource.org/licenses/MIT"
 *     )
 * )
 *
 * @OA\Server(
 *     url="http://127.0.0.1:8000",
 *     description="Local development server"
 * )
 * 
 * @OA\SecurityScheme(
 *     securityScheme="sanctum",
 *     type="http",
 *     scheme="bearer",
 *     bearerFormat="API Token"
 * )
 */
class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;
}
