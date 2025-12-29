
<?php
// Fichier : plat_index.php
// Rôle : Affiche la liste des plats pour la gestion employé (édition, suppression)
// Utilisé par : EmployePlatController::index()
require __DIR__ . '/../partials/header.php';
?>


<!-- Titre de la page -->
<h1>Gestion des plats</h1>


<!-- Lien vers la création d'un nouveau plat -->
<p>
    <a href="index.php?page=employe_plat_create">+ Créer un plat</a>
</p>


<!-- Messages de confirmation après action -->
<?php if (isset($_GET['created'])): ?><p>✅ Plat créé</p><?php endif; ?>
<?php if (isset($_GET['updated'])): ?><p>✅ Plat modifié</p><?php endif; ?>
<?php if (isset($_GET['deleted'])): ?><p>✅ Plat supprimé</p><?php endif; ?>


<!-- Tableau listant tous les plats avec actions d'édition et de suppression -->
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
                <!-- Lien pour éditer le plat -->
                <a href="index.php?page=employe_plat_edit&id=<?= (int)$p['id'] ?>">Éditer</a>

                <!-- Formulaire pour supprimer le plat (avec confirmation JS) -->
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
