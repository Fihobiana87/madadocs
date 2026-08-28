<section class="hero">
    <div class="container hero__grid">
        <div>
            <p class="eyebrow">Documents malgaches, sans complication</p>
            <h1>Vos documents. Sans complication.</h1>
            <p class="hero__lede">Créez rapidement vos documents administratifs et professionnels, directement depuis votre téléphone ou votre ordinateur.</p>
            <div class="hero__ctas">
                <a href="<?= base_url('/modeles') ?>" class="btn btn-primary">Créer un document</a>
                <a href="<?= base_url('/modeles') ?>" class="btn btn-secondary">Explorer les modèles</a>
            </div>
        </div>
        <div class="doc-stack" aria-hidden="true">
            <div class="sheet"></div>
            <div class="sheet"></div>
            <div class="sheet">
                <div class="line short"></div>
                <div class="line"></div>
                <div class="line"></div>
                <div class="line short"></div>
                <div class="stamp">MADA<br>DOCS</div>
            </div>
        </div>
    </div>
</section>

<?php if (!empty($popular)): ?>
<section class="section">
    <div class="container">
        <div class="section-head">
            <p class="eyebrow">Les plus utilisés</p>
            <h2>Documents populaires</h2>
        </div>
        <div class="grid-3">
            <?php foreach ($popular as $doc): ?>
                <a class="card-link" href="<?= base_url('/modeles/' . e($doc['slug'])) ?>">
                    <h3><?= e($doc['name']) ?></h3>
                    <p class="text-muted"><?= e($doc['description']) ?></p>
                    <span class="card-link__arrow">Créer →</span>
                </a>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<section class="section" style="border-top:1px solid var(--line)">
    <div class="container">
        <div class="section-head">
            <p class="eyebrow">Par situation</p>
            <h2>Catégories</h2>
        </div>
        <div class="grid-4">
            <?php foreach ($categories as $cat): ?>
                <a class="category-card" href="<?= base_url('/modeles#' . e($cat['slug'])) ?>">
                    <div class="category-card__icon" aria-hidden="true"><?= e($cat['icon']) ?></div>
                    <h3><?= e($cat['name']) ?></h3>
                    <div class="category-card__count"><?= e($cat['description']) ?></div>
                </a>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<section class="section" style="border-top:1px solid var(--line)">
    <div class="container">
        <div class="section-head">
            <p class="eyebrow">Comment ça marche</p>
            <h2>Trois étapes, deux minutes</h2>
        </div>
        <div class="steps">
            <div class="step">
                <h3>Choisissez</h3>
                <p>Sélectionnez le document qui correspond à votre situation parmi nos modèles malgaches.</p>
            </div>
            <div class="step">
                <h3>Remplissez</h3>
                <p>Seuls les champs utiles s'affichent. L'assistant IA peut vous aider à rédiger si besoin.</p>
            </div>
            <div class="step">
                <h3>Téléchargez</h3>
                <p>Prévisualisez votre document au format A4, puis téléchargez ou imprimez le PDF prêt à l'emploi.</p>
            </div>
        </div>
    </div>
</section>

<section class="section" style="border-top:1px solid var(--line)">
    <div class="container">
        <div class="cta-band">
            <h2>Prêt à créer votre document ?</h2>
            <p>Aucun compte n'est requis pour commencer.</p>
            <a href="<?= base_url('/modeles') ?>" class="btn btn-primary">Créer un document</a>
        </div>
    </div>
</section>
