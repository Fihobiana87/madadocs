<section class="section">
    <div class="container">
        <p class="eyebrow">Vos modèles préférés</p>
        <h1>Mes favoris</h1>

        <?php if (empty($favorites)): ?>
            <div class="empty-state">
                <div class="empty-state__icon">☆</div>
                <p>Aucun favori pour le moment. Ajoutez-en depuis une fiche modèle.</p>
                <a href="<?= base_url('/modeles') ?>" class="btn btn-primary">Explorer les modèles</a>
            </div>
        <?php else: ?>
            <div class="grid-3">
                <?php foreach ($favorites as $doc): ?>
                    <a class="card-link" href="<?= base_url('/modeles/' . e($doc['slug'])) ?>">
                        <h3><?= e($doc['name']) ?></h3>
                        <p class="text-muted"><?= e($doc['description']) ?></p>
                        <span class="card-link__arrow">Créer →</span>
                    </a>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</section>
