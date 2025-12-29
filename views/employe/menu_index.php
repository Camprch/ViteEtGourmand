
<?php
// Fichier : menu_index.php
// Rôle : Affiche la liste des menus pour la gestion employé (édition, activation, désactivation)
// Utilisé par : EmployeMenuController::index()
require __DIR__ . '/../partials/header.php';
?>


<!-- Titre de la page -->
<h1>Gestion des menus</h1>


<!-- Lien vers la création d'un nouveau menu -->
<p>
    <a href="index.php?page=employe_menu_create">+ Créer un menu</a>
</p>


<!-- Message de confirmation ou d'erreur après mise à jour -->
<?php if (isset($_GET['updated'])): ?>
    <p><?= $_GET['updated'] === '1' ? '✅ Menu mis à jour' : '❌ Échec mise à jour' ?></p>
<?php endif; ?>


<!-- Tableau listant tous les menus avec actions d'édition et d'activation/désactivation -->
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
                <!-- Lien pour éditer le menu -->
                <a href="index.php?page=employe_menu_edit&id=<?= (int)$m['id'] ?>">Éditer</a>

                <!-- Formulaire pour activer/désactiver le menu (stock) -->
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


<!-- Lien de retour vers le dashboard employé -->
<p><a href="index.php?page=dashboard_employe">Retour dashboard</a></p>


<?php require __DIR__ . '/../partials/footer.php'; ?>
