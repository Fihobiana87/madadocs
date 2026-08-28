<section class="section">
    <div class="container">
        <div class="section-head" style="margin:0 auto var(--sp-6);text-align:center">
            <p class="eyebrow">Tous les modèles</p>
            <h1>Quel document souhaitez-vous créer ?</h1>
        </div>

        <div class="search-box">
            <input class="input" type="search" placeholder="Rechercher, ex. « lettre pour demander un congé »" data-doc-search aria-label="Rechercher un modèle de document">
        </div>

        <?php foreach ($categories as $category): ?>
            <?php $docs = $documentsByCategory[$category['id']] ?? []; ?>
            <?php if (empty($docs)) continue; ?>
            <div id="<?= e($category['slug']) ?>" data-doc-section style="margin-bottom:var(--sp-7)">
                <h2><?= e($category['icon']) ?> <?= e($category['name']) ?></h2>
                <div class="grid-3">
                    <?php foreach ($docs as $doc): ?>
                        <a class="card-link" data-doc-card data-doc-keywords="<?= e(mb_strtolower($doc['name'] . ' ' . $doc['keywords'])) ?>" href="<?= base_url('/modeles/' . e($doc['slug'])) ?>">
                            <h3><?= e($doc['name']) ?></h3>
                            <p class="text-muted"><?= e($doc['description']) ?></p>
                            <span class="card-link__arrow">Créer →</span>
                        </a>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endforeach; ?>

        <div class="empty-state" data-search-empty style="display:none">
            <div class="empty-state__icon">🔍</div>
            <p>Aucun modèle ne correspond à votre recherche.</p>
        </div>
    </div>
</section>
