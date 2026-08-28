<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\DocumentTemplate;

class SitemapController extends Controller
{
    public function index(): void
    {
        header('Content-Type: application/xml; charset=utf-8');

        $urls = ['/', '/modeles', '/assistant'];
        foreach (DocumentTemplate::all() as $doc) {
            if ($doc['is_active']) {
                $urls[] = '/modeles/' . $doc['slug'];
            }
        }

        echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        echo '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";
        foreach ($urls as $url) {
            echo '  <url><loc>' . e(base_url($url)) . '</loc></url>' . "\n";
        }
        echo '</urlset>';
        exit;
    }
}
