<!doctype html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= isset($pageTitle) ? e($pageTitle) . ' — MadaDocs' : 'MadaDocs — Vos documents, sans complication' ?></title>
    <meta name="description" content="<?= e($pageDescription ?? "Créez vos documents administratifs et professionnels malgaches en quelques minutes : CV, lettres, demandes, factures.") ?>">
    <meta property="og:title" content="<?= e($pageTitle ?? 'MadaDocs') ?>">
    <meta property="og:description" content="<?= e($pageDescription ?? 'Créez rapidement vos documents administratifs et professionnels.') ?>">
    <meta property="og:type" content="website">
    <link rel="icon" href="data:image/svg+xml,<svg xmlns=%22http://www.w3.org/2000/svg%22 viewBox=%220 0 32 32%22><rect width=%2232%22 height=%2232%22 rx=%227%22 fill=%22%23bf4e2b%22/><text x=%2216%22 y=%2222%22 font-size=%2216%22 text-anchor=%22middle%22 fill=%22white%22 font-family=%22monospace%22>M</text></svg>">
    <link rel="stylesheet" href="<?= asset('css/main.css') ?>">
</head>
<body>
    <a href="#contenu" class="skip-link">Aller au contenu principal</a>

    <header class="site-header">
        <div class="container site-header__bar">
            <a href="<?= base_url('/') ?>" class="brand">
                <span class="brand__mark" aria-hidden="true">M</span>
                MadaDocs
            </a>
            <nav class="main-nav" data-main-nav aria-label="Navigation principale">
                <a href="<?= base_url('/modeles') ?>">Modèles</a>
                <a href="<?= base_url('/assistant') ?>">Assistant IA</a>
                <?php if (\App\Core\Auth::check()): ?>
                    <a href="<?= base_url('/tableau-de-bord') ?>">Mes documents</a>
                <?php endif; ?>
            </nav>
            <div class="header-actions">
                <?php if (\App\Core\Auth::check()): ?>
                    <a href="<?= base_url('/tableau-de-bord') ?>" class="btn btn-ghost btn-sm">Bonjour, <?= e(explode(' ', $_SESSION['user_name'])[0]) ?></a>
                    <form method="post" action="<?= base_url('/deconnexion') ?>" style="display:inline">
                        <?= csrf_field() ?>
                        <button class="btn btn-secondary btn-sm" type="submit">Déconnexion</button>
                    </form>
                <?php else: ?>
                    <a href="<?= base_url('/connexion') ?>" class="btn btn-ghost btn-sm">Connexion</a>
                    <a href="<?= base_url('/modeles') ?>" class="btn btn-primary btn-sm">Créer un document</a>
                <?php endif; ?>
                <button class="nav-toggle" data-nav-toggle aria-label="Ouvrir le menu" aria-expanded="false">☰</button>
            </div>
        </div>
    </header>

    <main id="contenu">
        <div class="container mt-6">
            <?php if ($msg = flash('success')): ?>
                <div class="alert alert-success" role="status"><span>✓</span><span><?= e($msg) ?></span></div>
            <?php endif; ?>
            <?php if ($msg = flash('error')): ?>
                <div class="alert alert-error" role="alert"><span>!</span><span><?= e($msg) ?></span></div>
            <?php endif; ?>
            <?php if ($msg = flash('info')): ?>
                <div class="alert alert-info" role="status"><span>ℹ</span><span><?= e($msg) ?></span></div>
            <?php endif; ?>
        </div>
        <?= $content ?>
    </main>

    <footer class="site-footer">
        <div class="container site-footer__grid">
            <div>
                <strong>MadaDocs</strong> — vos documents, sans complication.
            </div>
            <nav aria-label="Liens du pied de page">
                <a href="<?= base_url('/modeles') ?>">Modèles</a>
                &nbsp;·&nbsp;
                <a href="<?= base_url('/assistant') ?>">Assistant IA</a>
                &nbsp;·&nbsp;
                <a href="<?= base_url('/mentions-legales') ?>">Mentions légales</a>
            </nav>
        </div>
    </footer>

    <script src="<?= asset('js/main.js') ?>" defer></script>
</body>
</html>
