<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Category;
use App\Models\DocumentTemplate;

class HomeController extends Controller
{
    public function index(): void
    {
        $this->render('home/index', [
            'pageTitle' => 'Vos documents. Sans complication.',
            'pageDescription' => 'Créez rapidement vos documents administratifs et professionnels malgaches : CV, lettres, demandes, factures — directement depuis votre téléphone ou votre ordinateur.',
            'popular' => DocumentTemplate::popular(6),
            'categories' => Category::allOrdered(),
        ]);
    }

    public function legal(): void
    {
        $this->render('home/legal', ['pageTitle' => 'Mentions légales']);
    }
}
