<?php

// Vue : Menu index pour les employés

// Utilisé par : EmployeMenuController::index()
require __DIR__ . '/../partials/header.php';
?>


<section class="page-head">
    <div>
        <p class="eyebrow">Menus</p>
        <h1>Gestion des menus</h1>
    </div>
</section>

<section class="toolbar">
    <a class="btn" href="index.php?page=employe_menu_create">+ Créer un menu</a>
</section>

<?php if (isset($_GET['updated'])): ?>
    <section class="notice"><?= $_GET['updated'] === '1' ? '✅ Menu mis à jour' : '❌ Échec mise à jour' ?></section>
<?php endif; ?>

<section>
<div class="table-wrap">
<table class="table">
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
                <!-- Lien pour éditer le menu -->
                <a class="btn btn-ghost btn-sm" href="index.php?page=employe_menu_edit&id=<?= (int)$m['id'] ?>">Éditer</a>

                <!-- Formulaire pour activer/désactiver le menu (stock) -->
                <form method="post" action="index.php?page=employe_menu_toggle_stock" class="action-row">
                    <input type="hidden" name="_csrf" value="<?= htmlspecialchars(Csrf::token()) ?>">
                    <input type="hidden" name="id" value="<?= (int)$m['id'] ?>">
                    <input type="hidden" name="current_stock" value="<?= htmlspecialchars((string)($m['stock'] ?? '')) ?>">
                    <button class="btn-sm" type="submit">
                        <?= ((int)($m['stock'] ?? 1) === 0) ? 'Réactiver' : 'Désactiver' ?>
                    </button>
                </form>
            </td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>
</div>
</section>


<!-- Lien de retour vers le dashboard employé -->
<section class="cta-bar">
    <a class="btn btn-ghost" href="index.php?page=dashboard_employe">Retour dashboard</a>
</section>


<?php require __DIR__ . '/../partials/footer.php'; ?>
