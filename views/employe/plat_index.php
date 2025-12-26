<?php require __DIR__ . '/../partials/header.php'; ?>

<h1>Gestion des plats</h1>

<p>
    <a href="index.php?page=employe_plat_create">+ Créer un plat</a>
</p>

<?php if (isset($_GET['created'])): ?><p>✅ Plat créé</p><?php endif; ?>
<?php if (isset($_GET['updated'])): ?><p>✅ Plat modifié</p><?php endif; ?>
<?php if (isset($_GET['deleted'])): ?><p>✅ Plat supprimé</p><?php endif; ?>

<table border="1" cellpadding="6">
    <thead>
        <tr>
            <th>ID</th>
            <th>Type</th>
            <th>Nom</th>
            <th>Description</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
    <?php foreach ($plats as $p): ?>
        <tr>
            <td><?= (int)$p['id'] ?></td>
            <td><?= htmlspecialchars($p['type']) ?></td>
            <td><?= htmlspecialchars($p['nom']) ?></td>
            <td><?= htmlspecialchars($p['description'] ?? '') ?></td>
            <td>
                <a href="index.php?page=employe_plat_edit&id=<?= (int)$p['id'] ?>">Éditer</a>

                <form method="post" action="index.php?page=employe_plat_delete" style="display:inline" onsubmit="return confirm('Supprimer ce plat ?');">
                    <input type="hidden" name="_csrf" value="<?= htmlspecialchars(Csrf::token()) ?>">
                    <input type="hidden" name="id" value="<?= (int)$p['id'] ?>">
                    <button type="submit">Supprimer</button>
                </form>
            </td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>

<?php require __DIR__ . '/../partials/footer.php'; ?>
