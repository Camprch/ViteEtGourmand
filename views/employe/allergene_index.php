
<?php
// Fichier : allergene_index.php
// Rôle : Affiche la liste des allergènes avec actions de création, édition et suppression
// Utilisé par : EmployeAllergeneController::index()
require __DIR__ . '/../partials/header.php';
?>


<!-- Titre de la page -->
<h1>Allergènes</h1>


<!-- Lien vers la création d'un nouvel allergène -->
<p><a href="index.php?page=employe_allergene_create">+ Créer un allergène</a></p>


<!-- Affichage des messages de succès ou d'erreur -->
<?php if (isset($_GET['created'])): ?><p>✅ Créé</p><?php endif; ?>
<?php if (isset($_GET['updated'])): ?><p>✅ Modifié</p><?php endif; ?>
<?php if (isset($_GET['deleted'])): ?><p>✅ Supprimé</p><?php endif; ?>
<?php if (isset($_GET['delete_error'])): ?><p>❌ Impossible : allergène utilisé par un plat.</p><?php endif; ?>


<!-- Tableau listant tous les allergènes -->
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
                <!-- Lien pour éditer l'allergène -->
                <a href="index.php?page=employe_allergene_edit&id=<?= (int)$a['id'] ?>">Éditer</a>

                <!-- Formulaire pour supprimer l'allergène (avec confirmation JS) -->
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
