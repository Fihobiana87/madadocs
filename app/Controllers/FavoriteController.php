<?php

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Controller;
use App\Core\Csrf;
use App\Models\Favorite;

class FavoriteController extends Controller
{
    public function toggle(): void
    {
        if (!Auth::check()) {
            $this->json(['ok' => false, 'error' => 'unauthenticated'], 401);
        }

        if (!Csrf::verifyRequest()) {
            $this->json(['ok' => false, 'error' => 'invalid_csrf'], 419);
        }

        $documentId = (int) $this->input('document_id');
        $isFavorite = Favorite::toggle(Auth::id(), $documentId);

        $this->json(['ok' => true, 'favorite' => $isFavorite]);
    }
}
