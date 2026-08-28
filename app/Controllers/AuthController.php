<?php

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Controller;
use App\Core\Validator;
use App\Models\User;

class AuthController extends Controller
{
    public function showRegister(): void
    {
        if (Auth::check()) {
            redirect('/tableau-de-bord');
        }
        $this->render('auth/register', ['pageTitle' => 'Créer un compte']);
    }

    public function register(): void
    {
        $this->requireCsrf();

        $name = trim((string) $this->input('name', ''));
        $email = trim((string) $this->input('email', ''));
        $password = (string) $this->input('password', '');
        $passwordConfirm = (string) $this->input('password_confirm', '');

        $validator = new Validator([
            'name' => $name,
            'email' => $email,
            'password' => $password,
            'password_confirm' => $passwordConfirm,
        ]);
        $validator->required('name', 'Le nom')
            ->required('email', 'L’email')
            ->email('email')
            ->required('password', 'Le mot de passe')
            ->minLength('password', 8, 'Le mot de passe')
            ->matches('password_confirm', 'password', 'La confirmation du mot de passe');

        if (!$validator->fails() && User::findByEmail($email)) {
            $validator = new Validator([]);
            $errors = ['email' => 'Un compte existe déjà avec cet email.'];
        } else {
            $errors = $validator->errors();
        }

        if (!empty($errors)) {
            $_SESSION['_old'] = ['name' => $name, 'email' => $email];
            $this->render('auth/register', ['pageTitle' => 'Créer un compte', 'errors' => $errors]);
            return;
        }

        $userId = User::register($name, $email, $password);
        Auth::login(User::find($userId));
        unset($_SESSION['_old']);
        flash('success', 'Bienvenue sur MadaDocs !');
        redirect('/tableau-de-bord');
    }

    public function showLogin(): void
    {
        if (Auth::check()) {
            redirect('/tableau-de-bord');
        }
        $this->render('auth/login', ['pageTitle' => 'Connexion']);
    }

    public function login(): void
    {
        $this->requireCsrf();

        $email = trim((string) $this->input('email', ''));
        $password = (string) $this->input('password', '');

        $user = User::attempt($email, $password);

        if (!$user) {
            $_SESSION['_old'] = ['email' => $email];
            $this->render('auth/login', [
                'pageTitle' => 'Connexion',
                'errors' => ['email' => 'Email ou mot de passe incorrect.'],
            ]);
            return;
        }

        Auth::login($user);
        unset($_SESSION['_old']);
        redirect('/tableau-de-bord');
    }

    public function logout(): void
    {
        $this->requireCsrf();
        Auth::logout();
        redirect('/');
    }
}
