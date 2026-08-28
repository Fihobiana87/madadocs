<section class="section">
    <div class="container">
        <div class="dash-hello">
            <p class="eyebrow">Tableau de bord</p>
            <h1>Bonjour, <?= e(explode(' ', $_SESSION['user_name'])[0]) ?> 👋</h1>
        </div>

        <div class="flex-between" style="margin-bottom:var(--sp-4)">
            <h2 style="margin:0">Documents récents</h2>
            <a href="<?= base_url('/tableau-de-bord/favoris') ?>" class="btn btn-ghost btn-sm">Mes favoris</a>
        </div>

        <?php if (empty($recent)): ?>
            <div class="empty-state">
                <div class="empty-state__icon">📄</div>
                <p>Vous n’avez pas encore créé de document.</p>
            </div>
        <?php else: ?>
            <div class="doc-list">
                <?php foreach ($recent as $doc): ?>
                    <a class="doc-row" href="<?= base_url('/documents/' . (int) $doc['id'] . '/telecharger') ?>">
                        <div>
                            <strong><?= e($doc['document_name']) ?></strong>
                            <div class="doc-row__meta"><?= e(date('d/m/Y à H:i', strtotime($doc['created_at']))) ?></div>
                        </div>
                        <span class="badge">PDF</span>
                    </a>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <div class="cta-band mt-6">
            <h2>Créer un nouveau document</h2>
            <a href="<?= base_url('/modeles') ?>" class="btn btn-primary">Créer un document</a>
        </div>
    </div>
</section>
