<h1><?= $template ? 'Modifier le modèle' : 'Nouveau modèle' ?></h1>

<form method="post" action="<?= $template ? base_url('/admin/modeles/' . (int) $template['id'] . '/modifier') : base_url('/admin/modeles/nouveau') ?>" class="card">
    <?= csrf_field() ?>

    <div class="field-group-inline">
        <div class="field">
            <label for="name">Nom</label>
            <input class="input" id="name" name="name" type="text" required value="<?= e($template['name'] ?? '') ?>">
        </div>
        <div class="field">
            <label for="slug">Slug (URL)</label>
            <input class="input" id="slug" name="slug" type="text" required pattern="[a-z0-9\-]+" value="<?= e($template['slug'] ?? '') ?>">
        </div>
    </div>

    <div class="field-group-inline">
        <div class="field">
            <label for="category_id">Catégorie</label>
            <select class="input" id="category_id" name="category_id" required>
                <?php foreach ($categories as $cat): ?>
                    <option value="<?= (int) $cat['id'] ?>" <?= ($template['category_id'] ?? null) == $cat['id'] ? 'selected' : '' ?>><?= e($cat['name']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="field">
            <label for="pdf_view">Type de mise en page</label>
            <select class="input" id="pdf_view" name="pdf_view">
                <?php foreach (['letter' => 'Lettre', 'invoice' => 'Facture / devis', 'cv' => 'CV', 'attestation' => 'Attestation'] as $val => $label): ?>
                    <option value="<?= e($val) ?>" <?= ($template['pdf_view'] ?? 'letter') === $val ? 'selected' : '' ?>><?= e($label) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
    </div>

    <div class="field">
        <label for="description">Description</label>
        <input class="input" id="description" name="description" type="text" value="<?= e($template['description'] ?? '') ?>">
    </div>

    <div class="field">
        <label for="keywords">Mots-clés (recherche)</label>
        <input class="input" id="keywords" name="keywords" type="text" value="<?= e($template['keywords'] ?? '') ?>">
    </div>

    <div class="field">
        <label for="fields_schema">Champs du formulaire (JSON)</label>
        <textarea class="input" id="fields_schema" name="fields_schema" rows="8" style="font-family:var(--font-mono);font-size:0.85rem"><?= e($template['fields_schema'] ?? '[]') ?></textarea>
        <p class="hint">Tableau JSON d’objets <code>{"name","label","type","required","placeholder"}</code>. Types : text, email, date, number, textarea.</p>
    </div>

    <div class="field">
        <label for="subject_template">Objet (lettres uniquement, facultatif)</label>
        <input class="input" id="subject_template" name="subject_template" type="text" value="<?= e($template['subject_template'] ?? '') ?>">
    </div>

    <div class="field">
        <label for="body_template">Corps du texte (lettres/attestations, avec {{champ}})</label>
        <textarea class="input" id="body_template" name="body_template" rows="8"><?= e($template['body_template'] ?? '') ?></textarea>
    </div>

    <div class="field">
        <label><input type="checkbox" name="is_active" value="1" <?= ($template['is_active'] ?? 1) ? 'checked' : '' ?>> Modèle actif (visible sur le site)</label>
    </div>

    <button type="submit" class="btn btn-primary">Enregistrer</button>
</form>
