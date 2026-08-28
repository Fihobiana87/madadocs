<div class="flex-between">
    <h1>Modèles</h1>
    <a href="<?= base_url('/admin/modeles/nouveau') ?>" class="btn btn-primary btn-sm">+ Nouveau modèle</a>
</div>

<div class="table-wrap">
    <table class="data-table">
        <thead><tr><th>Nom</th><th>Catégorie</th><th>Type</th><th>Utilisations</th><th>Statut</th><th></th></tr></thead>
        <tbody>
            <?php foreach ($templates as $t): ?>
                <tr>
                    <td><?= e($t['name']) ?></td>
                    <td><?= e($t['category_name']) ?></td>
                    <td><span class="badge"><?= e($t['pdf_view']) ?></span></td>
                    <td><?= (int) $t['usage_count'] ?></td>
                    <td><?= $t['is_active'] ? 'Actif' : 'Inactif' ?></td>
                    <td style="white-space:nowrap">
                        <a href="<?= base_url('/admin/modeles/' . (int) $t['id'] . '/modifier') ?>" class="btn btn-ghost btn-sm">Modifier</a>
                        <form method="post" action="<?= base_url('/admin/modeles/' . (int) $t['id'] . '/supprimer') ?>" style="display:inline" data-confirm="Supprimer ce modèle ?">
                            <?= csrf_field() ?>
                            <button type="submit" class="btn btn-ghost btn-sm">Supprimer</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
