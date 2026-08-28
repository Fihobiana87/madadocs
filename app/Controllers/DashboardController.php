<?php

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Controller;
use App\Models\Favorite;
use App\Models\GeneratedDocument;

class DashboardController extends Controller
{
    public function index(): void
    {
        $this->requireAuth();

        $this->render('dashboard/index', [
            'pageTitle' => 'Tableau de bord',
            'recent' => GeneratedDocument::recentForUser(Auth::id(), 8),
        ]);
    }

    public function favorites(): void
    {
        $this->requireAuth();

        $this->render('dashboard/favorites', [
            'pageTitle' => 'Mes favoris',
            'favorites' => Favorite::forUser(Auth::id()),
        ]);
    }
}
