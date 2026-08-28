<h1>Catégories</h1>

<div class="table-wrap" style="margin-bottom:var(--sp-6)">
    <table class="data-table">
        <thead><tr><th>Icône</th><th>Nom</th><th>Slug</th><th>Description</th><th>Position</th></tr></thead>
        <tbody>
            <?php foreach ($categories as $cat): ?>
                <tr>
                    <td><?= e($cat['icon']) ?></td>
                    <td><?= e($cat['name']) ?></td>
                    <td><code><?= e($cat['slug']) ?></code></td>
                    <td><?= e($cat['description']) ?></td>
                    <td><?= (int) $cat['position'] ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<div class="card" style="max-width:520px">
    <h3>Nouvelle catégorie</h3>
    <form method="post" action="<?= base_url('/admin/categories/nouveau') ?>">
        <?= csrf_field() ?>
        <div class="field">
            <label for="name">Nom</label>
            <input class="input" id="name" name="name" type="text" required>
        </div>
        <div class="field">
            <label for="slug">Slug</label>
            <input class="input" id="slug" name="slug" type="text" required pattern="[a-z0-9\-]+">
        </div>
        <div class="field">
            <label for="icon">Icône (emoji)</label>
            <input class="input" id="icon" name="icon" type="text" value="📄">
        </div>
        <div class="field">
            <label for="description">Description</label>
            <input class="input" id="description" name="description" type="text">
        </div>
        <div class="field">
            <label for="position">Position (ordre d’affichage)</label>
            <input class="input" id="position" name="position" type="number" value="0">
        </div>
        <button type="submit" class="btn btn-primary">Créer</button>
    </form>
</div>
