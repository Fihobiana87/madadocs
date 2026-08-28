<?php

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Controller;
use App\Core\DocumentRenderer;
use App\Core\PdfRenderer;
use App\Core\View;
use App\Models\DocumentTemplate;
use App\Models\Favorite;
use App\Models\GeneratedDocument;

class DocumentController extends Controller
{
    public function show(string $slug): void
    {
        $template = DocumentTemplate::findBySlug($slug);

        if (!$template || !$template['is_active']) {
            http_response_code(404);
            $this->render('errors/404');
            return;
        }

        $fields = DocumentTemplate::fields($template);
        $skeleton = DocumentRenderer::skeleton($template);
        $isFavorite = Auth::check() && Favorite::isFavorite(Auth::id(), $template['id']);

        $view = $template['pdf_view'] === 'invoice' ? 'documents/show_invoice' : 'documents/show';

        $this->render($view, [
            'pageTitle' => $template['name'],
            'pageDescription' => $template['description'],
            'template' => $template,
            'fields' => $fields,
            'skeleton' => $skeleton,
            'isFavorite' => $isFavorite,
        ]);
    }

    public function generate(string $slug): void
    {
        $this->requireCsrf();

        $template = DocumentTemplate::findBySlug($slug);
        if (!$template || !$template['is_active']) {
            http_response_code(404);
            $this->render('errors/404');
            return;
        }

        $fields = DocumentTemplate::fields($template);
        $data = [];
        $missing = [];

        foreach ($fields as $field) {
            $value = trim((string) $this->input($field['name'], ''));
            if (!empty($field['required']) && $value === '') {
                $missing[] = $field['label'];
            }
            $data[$field['name']] = $value;
        }

        if ($template['pdf_view'] === 'invoice') {
            $items = $this->collectInvoiceItems();
            if (empty($items)) {
                $missing[] = 'Au moins une ligne de produit ou service';
            }
        }

        if (!empty($missing)) {
            flash('error', 'Champs manquants : ' . implode(', ', $missing));
            redirect('/modeles/' . $slug);
        }

        $bodyHtml = $this->renderDocumentBody($template, $data, $items ?? []);

        ob_start();
        View::partial('documents/pdf/_style');
        $style = ob_get_clean();

        $fullHtml = '<!doctype html><html><head><meta charset="utf-8">' . $style . '</head><body>' . $bodyHtml . '</body></html>';

        $fileName = $this->safeFileName($template['slug']) . '-' . date('Ymd-His') . '.pdf';
        PdfRenderer::render($fullHtml, $fileName);

        $generatedId = GeneratedDocument::create([
            'user_id' => Auth::id(),
            'document_id' => $template['id'],
            'file_name' => $fileName,
            'data_json' => json_encode(array_merge($data, ['produits' => $items ?? []]), JSON_UNESCAPED_UNICODE),
            'created_at' => date('Y-m-d H:i:s'),
        ]);

        DocumentTemplate::incrementUsage($template['id']);

        flash('success', 'Votre document est prêt.');
        redirect('/documents/' . $generatedId . '/telecharger?t=' . signed_token($generatedId));
    }

    public function download(int $id): void
    {
        $generated = GeneratedDocument::find($id);

        if (!$generated) {
            http_response_code(404);
            $this->render('errors/404');
            return;
        }

        if ($generated['user_id'] !== null) {
            $owner = (int) $generated['user_id'] === Auth::id();
            $isAdmin = ($_SESSION['user_role'] ?? '') === 'admin';
            if (!$owner && !$isAdmin) {
                http_response_code(403);
                $this->render('errors/403');
                return;
            }
        } else {
            $token = (string) $this->input('t', '');
            if (!hash_equals(signed_token($id), $token)) {
                http_response_code(403);
                $this->render('errors/403');
                return;
            }
        }

        $path = dirname(__DIR__, 2) . '/storage/generated/' . $generated['file_name'];

        if (!is_file($path)) {
            http_response_code(404);
            $this->render('errors/404');
            return;
        }

        header('Content-Type: application/pdf');
        header('Content-Disposition: inline; filename="' . $generated['file_name'] . '"');
        header('Content-Length: ' . filesize($path));
        readfile($path);
        exit;
    }

    private function renderDocumentBody(array $template, array $data, array $items): string
    {
        if ($template['pdf_view'] === 'cv') {
            return DocumentRenderer::renderCv($data);
        }

        $skeleton = DocumentRenderer::skeleton($template);

        if ($template['pdf_view'] !== 'invoice') {
            return fill_template($skeleton, $data);
        }

        $html = fill_template($skeleton, $data);
        $tva = (float) ($data['tva'] ?? 0);

        $rows = '';
        $subtotal = 0.0;
        foreach ($items as $item) {
            $lineTotal = $item['quantite'] * $item['prix'];
            $subtotal += $lineTotal;
            $rows .= '<tr>'
                . '<td>' . e($item['description']) . '</td>'
                . '<td class="num">' . number_format($item['quantite'], 2, ',', ' ') . '</td>'
                . '<td class="num">' . number_format($item['prix'], 2, ',', ' ') . ' Ar</td>'
                . '<td class="num">' . number_format($lineTotal, 2, ',', ' ') . ' Ar</td>'
                . '</tr>';
        }

        $taxAmount = $subtotal * ($tva / 100);
        $total = $subtotal + $taxAmount;

        $table = '<table class="invoice-items"><thead><tr>'
            . '<th>Description</th><th class="num">Qté</th><th class="num">Prix unitaire</th><th class="num">Total</th>'
            . '</tr></thead><tbody>' . $rows . '</tbody></table>'
            . '<div class="invoice-totals"><table>'
            . '<tr><td>Sous-total</td><td class="num">' . number_format($subtotal, 2, ',', ' ') . ' Ar</td></tr>'
            . '<tr><td>TVA (' . number_format($tva, 0) . '%)</td><td class="num">' . number_format($taxAmount, 2, ',', ' ') . ' Ar</td></tr>'
            . '<tr class="grand-total"><td>Total</td><td class="num">' . number_format($total, 2, ',', ' ') . ' Ar</td></tr>'
            . '</table></div>';

        return str_replace('<div data-invoice-table></div>', $table, $html);
    }

    private function collectInvoiceItems(): array
    {
        $descriptions = $_POST['produit_description'] ?? [];
        $quantities = $_POST['produit_quantite'] ?? [];
        $prices = $_POST['produit_prix'] ?? [];

        $items = [];
        foreach ($descriptions as $i => $description) {
            $description = trim((string) $description);
            $qty = (float) ($quantities[$i] ?? 0);
            $price = (float) ($prices[$i] ?? 0);

            if ($description === '' || $qty <= 0) {
                continue;
            }

            $items[] = ['description' => $description, 'quantite' => $qty, 'prix' => $price];
        }

        return $items;
    }

    private function safeFileName(string $slug): string
    {
        return preg_replace('/[^a-z0-9\-]/', '', strtolower($slug));
    }
}
