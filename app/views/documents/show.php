<section class="section" style="padding-top:var(--sp-6)">
    <div class="container">
        <div class="flex-between mt-6" style="margin-bottom:var(--sp-5)">
            <div>
                <p class="eyebrow"><?= e($template['name']) ?></p>
                <h1 style="margin-bottom:4px"><?= e($template['name']) ?></h1>
                <p class="text-muted" style="margin:0"><?= e($template['description']) ?></p>
            </div>
            <?php if (\App\Core\Auth::check()): ?>
                <button type="button" class="btn btn-ghost btn-sm <?= $isFavorite ? 'is-active' : '' ?>"
                    data-favorite-toggle data-document-id="<?= (int) $template['id'] ?>" data-csrf="<?= e(\App\Core\Csrf::token()) ?>">
                    <?= $isFavorite ? '★ Favori' : '☆ Ajouter aux favoris' ?>
                </button>
            <?php endif; ?>
        </div>

        <div class="doc-progress" data-progress><span></span><span></span><span></span><span></span></div>

        <div class="generator">
            <div class="generator__form">
                <form method="post" action="<?= base_url('/modeles/' . e($template['slug']) . '/generer') ?>" data-generator-form data-doc-slug="<?= e($template['slug']) ?>">
                    <?= csrf_field() ?>
                    <?php foreach ($fields as $field): ?>
                        <?php \App\Core\View::partial('partials/field', ['field' => $field]); ?>
                    <?php endforeach; ?>

                    <div class="assistant-box">
                        <h4>Besoin d’aide pour rédiger ?</h4>
                        <p class="text-muted" style="margin:0">L’assistant MadaDocs peut proposer un texte adapté à votre situation.</p>
                        <a href="<?= base_url('/assistant?modele=' . e($template['slug'])) ?>" class="btn btn-secondary btn-sm">Ouvrir l’assistant IA</a>
                    </div>

                    <div class="hero__ctas">
                        <button type="submit" class="btn btn-primary">Générer le PDF</button>
                        <button type="button" class="btn btn-ghost" data-clear-draft>Effacer le brouillon</button>
                    </div>
                </form>
            </div>

            <div class="generator__preview-col">
                <div class="a4-frame">
                    <div class="a4-frame__inner">
                        <div data-preview data-template="<?= e($skeleton) ?>"></div>
                    </div>
                </div>
                <p class="text-muted" style="text-align:center;font-size:0.82rem;margin-top:8px">Aperçu — le PDF final aura la mise en page A4 complète.</p>
            </div>
        </div>
    </div>
</section>
