<!doctype html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= e($pageTitle ?? 'Administration') ?> — MadaDocs</title>
    <meta name="robots" content="noindex, nofollow">
    <link rel="stylesheet" href="<?= asset('css/main.css') ?>">
</head>
<body>
    <header class="site-header">
        <div class="container site-header__bar">
            <a href="<?= base_url('/admin') ?>" class="brand">
                <span class="brand__mark" aria-hidden="true">M</span>
                MadaDocs <span class="badge">Admin</span>
            </a>
            <div class="header-actions">
                <a href="<?= base_url('/') ?>" class="btn btn-ghost btn-sm">Voir le site</a>
                <form method="post" action="<?= base_url('/deconnexion') ?>">
                    <?= csrf_field() ?>
                    <button class="btn btn-secondary btn-sm" type="submit">Déconnexion</button>
                </form>
            </div>
        </div>
    </header>

    <main>
        <div class="container mt-6">
            <?php if ($msg = flash('success')): ?>
                <div class="alert alert-success"><span>✓</span><span><?= e($msg) ?></span></div>
            <?php endif; ?>
            <?php if ($msg = flash('error')): ?>
                <div class="alert alert-error"><span>!</span><span><?= e($msg) ?></span></div>
            <?php endif; ?>

            <div class="admin-shell">
                <nav class="admin-nav" aria-label="Navigation admin">
                    <a href="<?= base_url('/admin') ?>">Tableau de bord</a>
                    <a href="<?= base_url('/admin/modeles') ?>">Modèles</a>
                    <a href="<?= base_url('/admin/categories') ?>">Catégories</a>
                    <a href="<?= base_url('/admin/utilisateurs') ?>">Utilisateurs</a>
                    <a href="<?= base_url('/admin/documents-generes') ?>">Documents générés</a>
                </nav>
                <div><?= $content ?></div>
            </div>
        </div>
    </main>
</body>
</html>
