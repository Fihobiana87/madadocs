<h1>Documents générés</h1>

<?php if (empty($documents)): ?>
    <p class="text-muted">Aucun document généré pour le moment.</p>
<?php else: ?>
    <div class="table-wrap">
        <table class="data-table">
            <thead><tr><th>Document</th><th>Utilisateur</th><th>Date</th></tr></thead>
            <tbody>
                <?php foreach ($documents as $doc): ?>
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
