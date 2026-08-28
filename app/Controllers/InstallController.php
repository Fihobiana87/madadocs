<?php

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Controller;
use App\Core\Validator;
use App\Models\User;

/**
 * Création sécurisée du tout premier compte administrateur, sans identifiant
 * codé en dur ni accès SSH requis (contrainte InfinityFree). Cette route se
 * désactive d'elle-même dès qu'un admin existe déjà.
 */
class InstallController extends Controller
{
    public function show(): void
    {
        if ($this->adminExists()) {
            flash('info', 'Un compte administrateur existe déjà. Connectez-vous normalement.');
            redirect('/connexion');
        }

        $this->render('auth/install', ['pageTitle' => 'Créer le compte administrateur']);
    }

    public function store(): void
    {
        if ($this->adminExists()) {
            redirect('/connexion');
        }

        $this->requireCsrf();

        $name = trim((string) $this->input('name', ''));
        $email = trim((string) $this->input('email', ''));
        $password = (string) $this->input('password', '');
        $passwordConfirm = (string) $this->input('password_confirm', '');

        $validator = new Validator([
            'name' => $name, 'email' => $email,
            'password' => $password, 'password_confirm' => $passwordConfirm,
        ]);
        $validator->required('name', 'Le nom')
            ->required('email', 'L’email')
            ->email('email')
            ->required('password', 'Le mot de passe')
            ->minLength('password', 10, 'Le mot de passe administrateur')
            ->matches('password_confirm', 'password', 'La confirmation du mot de passe');

        if ($validator->fails() || User::findByEmail($email)) {
            $errors = $validator->fails() ? $validator->errors() : ['email' => 'Cet email est déjà utilisé.'];
            $_SESSION['_old'] = ['name' => $name, 'email' => $email];
            $this->render('auth/install', ['pageTitle' => 'Créer le compte administrateur', 'errors' => $errors]);
            return;
        }

        $userId = User::register($name, $email, $password);
        User::update($userId, ['role' => 'admin']);
        Auth::login(User::find($userId));

        flash('success', 'Compte administrateur créé. Bienvenue sur MadaDocs.');
        redirect('/admin');
    }

    private function adminExists(): bool
    {
        return (bool) User::where('role', 'admin');
    }
}
