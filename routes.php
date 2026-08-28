<?php

use App\Core\Router;
use App\Controllers\HomeController;
use App\Controllers\AuthController;
use App\Controllers\CategoryController;
use App\Controllers\DocumentController;
use App\Controllers\FavoriteController;
use App\Controllers\AiController;
use App\Controllers\DashboardController;
use App\Controllers\AdminController;

$router = new Router();

$router->get('/', [HomeController::class, 'index']);
$router->get('/mentions-legales', [HomeController::class, 'legal']);

$router->get('/inscription', [AuthController::class, 'showRegister']);
$router->post('/inscription', [AuthController::class, 'register']);
$router->get('/connexion', [AuthController::class, 'showLogin']);
$router->post('/connexion', [AuthController::class, 'login']);
$router->post('/deconnexion', [AuthController::class, 'logout']);

$router->get('/modeles', [CategoryController::class, 'index']);
$router->get('/modeles/{slug}', [DocumentController::class, 'show']);
$router->post('/modeles/{slug}/generer', [DocumentController::class, 'generate']);
$router->get('/documents/{id}/telecharger', [DocumentController::class, 'download']);

$router->post('/favoris/basculer', [FavoriteController::class, 'toggle']);

$router->get('/assistant', [AiController::class, 'index']);
$router->post('/assistant/generer', [AiController::class, 'generate']);
$router->post('/assistant/ameliorer', [AiController::class, 'improve']);

$router->get('/tableau-de-bord', [DashboardController::class, 'index']);
$router->get('/tableau-de-bord/favoris', [DashboardController::class, 'favorites']);

$router->get('/admin', [AdminController::class, 'dashboard']);
$router->get('/admin/modeles', [AdminController::class, 'templates']);
$router->get('/admin/modeles/nouveau', [AdminController::class, 'createTemplateForm']);
$router->post('/admin/modeles/nouveau', [AdminController::class, 'storeTemplate']);
$router->get('/admin/modeles/{id}/modifier', [AdminController::class, 'editTemplateForm']);
$router->post('/admin/modeles/{id}/modifier', [AdminController::class, 'updateTemplate']);
$router->post('/admin/modeles/{id}/supprimer', [AdminController::class, 'deleteTemplate']);
$router->get('/admin/categories', [AdminController::class, 'categories']);
$router->post('/admin/categories/nouveau', [AdminController::class, 'storeCategory']);
$router->get('/admin/utilisateurs', [AdminController::class, 'users']);
$router->post('/admin/utilisateurs/{id}/role', [AdminController::class, 'updateUserRole']);
$router->get('/admin/documents-generes', [AdminController::class, 'generatedDocuments']);
