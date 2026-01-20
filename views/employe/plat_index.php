<?php

// Vue : index des plats

// Utilisé par : EmployePlatController::index()
require __DIR__ . '/../partials/header.php';
?>


<section class="page-head">
    <div>
        <p class="eyebrow">Plats</p>
        <h1>Gestion des plats</h1>
    </div>
</section>

<section class="toolbar">
    <a class="btn" href="index.php?page=employe_plat_create">+ Créer un plat</a>
</section>

<?php if (isset($_GET['created'])): ?><section class="notice">✅ Plat créé</section><?php endif; ?>
<?php if (isset($_GET['updated'])): ?><section class="notice">✅ Plat modifié</section><?php endif; ?>
<?php if (isset($_GET['deleted'])): ?><section class="notice">✅ Plat supprimé</section><?php endif; ?>

<section>
<div class="table-wrap">
<table class="table">
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
                <!-- Lien pour éditer le plat -->
                <a class="btn btn-ghost btn-sm" href="index.php?page=employe_plat_edit&id=<?= (int)$p['id'] ?>">Éditer</a>

                <!-- Formulaire pour supprimer le plat (avec confirmation JS) -->
                <form method="post" action="index.php?page=employe_plat_delete" class="action-row" onsubmit="return confirm('Supprimer ce plat ?');">
                    <input type="hidden" name="_csrf" value="<?= htmlspecialchars(Csrf::token()) ?>">
                    <input type="hidden" name="id" value="<?= (int)$p['id'] ?>">
                    <button class="btn btn-ghost btn-sm" type="submit">Supprimer</button>
                </form>
            </td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>
</div>
</section>


<?php require __DIR__ . '/../partials/footer.php'; ?>
