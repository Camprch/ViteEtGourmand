<?php require __DIR__ . '/../partials/header.php'; ?>

<h1>Allergènes</h1>

<p><a href="index.php?page=employe_allergene_create">+ Créer un allergène</a></p>

<?php if (isset($_GET['created'])): ?><p>✅ Créé</p><?php endif; ?>
<?php if (isset($_GET['updated'])): ?><p>✅ Modifié</p><?php endif; ?>
<?php if (isset($_GET['deleted'])): ?><p>✅ Supprimé</p><?php endif; ?>
<?php if (isset($_GET['delete_error'])): ?><p>❌ Impossible : allergène utilisé par un plat.</p><?php endif; ?>

<table border="1" cellpadding="6">
    <thead>
        <tr>
            <th>ID</th>
            <th>Nom</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
    <?php foreach ($allergenes as $a): ?>
        <tr>
            <td><?= (int)$a['id'] ?></td>
            <td><?= htmlspecialchars($a['nom']) ?></td>
            <td>
                <a href="index.php?page=employe_allergene_edit&id=<?= (int)$a['id'] ?>">Éditer</a>

                <form method="post" action="index.php?page=employe_allergene_delete" style="display:inline" onsubmit="return confirm('Supprimer ?');">
                    <input type="hidden" name="_csrf" value="<?= htmlspecialchars(Csrf::token()) ?>">
                    <input type="hidden" name="id" value="<?= (int)$a['id'] ?>">
                    <button type="submit">Supprimer</button>
                </form>
            </td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>

<?php require __DIR__ . '/../partials/footer.php'; ?>
