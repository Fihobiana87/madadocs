<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Category;
use App\Models\DocumentTemplate;
use App\Models\GeneratedDocument;
use App\Models\User;

class AdminController extends Controller
{
    public function __construct()
    {
        $this->requireAdmin();
    }

    public function dashboard(): void
    {
        $this->render('admin/dashboard', [
            'pageTitle' => 'Administration',
            'stats' => [
                'documents' => DocumentTemplate::count(),
                'categories' => Category::count(),
                'users' => User::count(),
                'generated' => GeneratedDocument::count(),
            ],
            'recentGenerated' => GeneratedDocument::allWithDetails(6),
        ], 'admin');
    }

    public function templates(): void
    {
        $this->render('admin/templates', [
            'pageTitle' => 'Modèles',
            'templates' => DocumentTemplate::allWithCategory(),
        ], 'admin');
    }

    public function createTemplateForm(): void
    {
        $this->render('admin/template_form', [
            'pageTitle' => 'Nouveau modèle',
            'categories' => Category::allOrdered(),
            'template' => null,
        ], 'admin');
    }

    public function storeTemplate(): void
    {
        $this->requireCsrf();
        $this->saveTemplate(null);
    }

    public function editTemplateForm(int $id): void
    {
        $template = DocumentTemplate::find($id);
        if (!$template) {
            http_response_code(404);
            $this->render('errors/404');
            return;
        }

        $this->render('admin/template_form', [
            'pageTitle' => 'Modifier ' . $template['name'],
            'categories' => Category::allOrdered(),
            'template' => $template,
        ], 'admin');
    }

    public function updateTemplate(int $id): void
    {
        $this->requireCsrf();
        $this->saveTemplate($id);
    }

    private function saveTemplate(?int $id): void
    {
        $fieldsSchema = trim((string) $this->input('fields_schema', '[]'));
        json_decode($fieldsSchema);
        if (json_last_error() !== JSON_ERROR_NONE) {
            flash('error', 'Le schéma des champs (JSON) est invalide : ' . json_last_error_msg());
            redirect($id ? "/admin/modeles/{$id}/modifier" : '/admin/modeles/nouveau');
        }

        $data = [
            'category_id' => (int) $this->input('category_id'),
            'slug' => trim((string) $this->input('slug')),
            'name' => trim((string) $this->input('name')),
            'description' => trim((string) $this->input('description', '')),
            'keywords' => trim((string) $this->input('keywords', '')),
            'pdf_view' => trim((string) $this->input('pdf_view', 'letter')),
            'fields_schema' => $fieldsSchema,
            'subject_template' => trim((string) $this->input('subject_template', '')) ?: null,
            'body_template' => trim((string) $this->input('body_template', '')) ?: null,
            'is_active' => $this->input('is_active') ? 1 : 0,
        ];

        if ($id) {
            DocumentTemplate::update($id, $data);
            flash('success', 'Modèle mis à jour.');
        } else {
            $data['usage_count'] = 0;
            $data['created_at'] = date('Y-m-d H:i:s');
            DocumentTemplate::create($data);
            flash('success', 'Modèle créé.');
        }

        redirect('/admin/modeles');
    }

    public function deleteTemplate(int $id): void
    {
        $this->requireCsrf();
        DocumentTemplate::delete($id);
        flash('success', 'Modèle supprimé.');
        redirect('/admin/modeles');
    }

    public function categories(): void
    {
        $this->render('admin/categories', [
            'pageTitle' => 'Catégories',
            'categories' => Category::allOrdered(),
        ], 'admin');
    }

    public function storeCategory(): void
    {
        $this->requireCsrf();

        Category::create([
            'slug' => trim((string) $this->input('slug')),
            'name' => trim((string) $this->input('name')),
            'description' => trim((string) $this->input('description', '')),
            'icon' => trim((string) $this->input('icon', '📄')),
            'position' => (int) $this->input('position', 0),
        ]);

        flash('success', 'Catégorie créée.');
        redirect('/admin/categories');
    }

    public function users(): void
    {
        $this->render('admin/users', [
            'pageTitle' => 'Utilisateurs',
            'users' => User::all('name ASC'),
        ], 'admin');
    }

    public function updateUserRole(int $id): void
    {
        $this->requireCsrf();
        $role = $this->input('role') === 'admin' ? 'admin' : 'user';
        User::update($id, ['role' => $role]);
        flash('success', 'Rôle mis à jour.');
        redirect('/admin/utilisateurs');
    }

    public function generatedDocuments(): void
    {
        $this->render('admin/generated', [
            'pageTitle' => 'Documents générés',
            'documents' => GeneratedDocument::allWithDetails(200),
        ], 'admin');
    }
}
