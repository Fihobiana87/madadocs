<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Category;
use App\Models\DocumentTemplate;

class CategoryController extends Controller
{
    public function index(): void
    {
        $categories = Category::allOrdered();
        $documentsByCategory = [];

        foreach ($categories as $category) {
            $documentsByCategory[$category['id']] = DocumentTemplate::activeByCategory($category['id']);
        }

        $this->render('documents/browse', [
            'pageTitle' => 'Modèles de documents',
            'pageDescription' => 'Tous les modèles de documents administratifs et professionnels malgaches disponibles sur MadaDocs.',
            'categories' => $categories,
            'documentsByCategory' => $documentsByCategory,
        ]);
    }
}
