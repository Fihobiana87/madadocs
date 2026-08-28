<h1>Tableau de bord</h1>

<div class="grid-4" style="margin-bottom:var(--sp-6)">
    <div class="stat-tile">
        <div class="stat-tile__value"><?= (int) $stats['documents'] ?></div>
        <div class="stat-tile__label">Modèles actifs</div>
    </div>
    <div class="stat-tile">
        <div class="stat-tile__value"><?= (int) $stats['categories'] ?></div>
        <div class="stat-tile__label">Catégories</div>
    </div>
    <div class="stat-tile">
        <div class="stat-tile__value"><?= (int) $stats['users'] ?></div>
        <div class="stat-tile__label">Utilisateurs</div>
    </div>
    <div class="stat-tile">
        <div class="stat-tile__value"><?= (int) $stats['generated'] ?></div>
        <div class="stat-tile__label">Documents générés</div>
    </div>
</div>

<h2>Derniers documents générés</h2>
<?php if (empty($recentGenerated)): ?>
    <p class="text-muted">Aucun document généré pour le moment.</p>
<?php else: ?>
    <div class="table-wrap">
        <table class="data-table">
            <thead><tr><th>Document</th><th>Utilisateur</th><th>Date</th></tr></thead>
            <tbody>
                <?php foreach ($recentGenerated as $doc): ?>
                    <tr>
                        <td><?= e($doc['document_name']) ?></td>
                        <td><?= e($doc['user_name'] ?? 'Anonyme') ?></td>
                        <td><?= e(date('d/m/Y H:i', strtotime($doc['created_at']))) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
<?php endif; ?>
