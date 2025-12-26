<?php require __DIR__ . '/../partials/header.php'; ?>

<h1>Gestion des menus</h1>

<p>
    <a href="index.php?page=employe_menu_create">+ Créer un menu</a>
</p>

<?php if (isset($_GET['updated'])): ?>
    <p><?= $_GET['updated'] === '1' ? '✅ Menu mis à jour' : '❌ Échec mise à jour' ?></p>
<?php endif; ?>

<table border="1" cellpadding="6">
    <thead>
        <tr>
            <th>ID</th>
            <th>Titre</th>
            <th>Thème</th>
            <th>Régime</th>
            <th>Min</th>
            <th>Prix</th>
            <th>Stock</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
    <?php foreach ($menus as $m): ?>
        <tr>
            <td><?= (int)$m['id'] ?></td>
            <td><?= htmlspecialchars($m['titre']) ?></td>
            <td><?= htmlspecialchars($m['theme'] ?? '') ?></td>
            <td><?= htmlspecialchars($m['regime'] ?? '') ?></td>
            <td><?= (int)$m['personnes_min'] ?></td>
            <td><?= htmlspecialchars((string)$m['prix_par_personne']) ?></td>
            <td>
                <?php
                    $stock = $m['stock'];
                    echo ($stock === null) ? '∞' : (int)$stock;
                ?>
            </td>
            <td>
                <a href="index.php?page=employe_menu_edit&id=<?= (int)$m['id'] ?>">Éditer</a>

                <form method="post" action="index.php?page=employe_menu_toggle_stock" style="display:inline">
                    <input type="hidden" name="_csrf" value="<?= htmlspecialchars(Csrf::token()) ?>">
                    <input type="hidden" name="id" value="<?= (int)$m['id'] ?>">
                    <input type="hidden" name="current_stock" value="<?= htmlspecialchars((string)($m['stock'] ?? '')) ?>">
                    <button type="submit">
                        <?= ((int)($m['stock'] ?? 1) === 0) ? 'Réactiver' : 'Désactiver' ?>
                    </button>
                </form>
            </td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>

<p><a href="index.php?page=dashboard_employe">Retour dashboard</a></p>

<?php require __DIR__ . '/../partials/footer.php'; ?>
