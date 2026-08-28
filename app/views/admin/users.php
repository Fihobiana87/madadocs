<h1>Utilisateurs</h1>

<div class="table-wrap">
    <table class="data-table">
        <thead><tr><th>Nom</th><th>Email</th><th>Rôle</th><th>Inscrit le</th><th></th></tr></thead>
        <tbody>
            <?php foreach ($users as $u): ?>
                <tr>
                    <td><?= e($u['name']) ?></td>
                    <td><?= e($u['email']) ?></td>
                    <td><span class="badge"><?= e($u['role']) ?></span></td>
                    <td><?= e(date('d/m/Y', strtotime($u['created_at']))) ?></td>
                    <td>
                        <form method="post" action="<?= base_url('/admin/utilisateurs/' . (int) $u['id'] . '/role') ?>" style="display:flex;gap:8px">
                            <?= csrf_field() ?>
                            <select class="input" name="role" style="min-height:36px;padding:0.3rem 0.6rem">
                                <option value="user" <?= $u['role'] === 'user' ? 'selected' : '' ?>>Utilisateur</option>
                                <option value="admin" <?= $u['role'] === 'admin' ? 'selected' : '' ?>>Admin</option>
                            </select>
                            <button type="submit" class="btn btn-ghost btn-sm">Mettre à jour</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
