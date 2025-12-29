<?php

// Vue : formulaire d'édition d'un allergène

// Utilisé par : EmployeAllergeneController::edit()

require __DIR__ . '/../partials/header.php';
?>

<!-- Titre de la page -->
<h1>Modifier un allergène</h1>

<!-- Formulaire d'édition d'un allergène -->
<form method="post" action="index.php?page=employe_allergene_update">
    <!-- Protection CSRF -->
    <input type="hidden" name="_csrf" value="<?= htmlspecialchars(Csrf::token()) ?>">
    <!-- Identifiant de l'allergène à modifier -->
    <input type="hidden" name="id" value="<?= (int)$allergene['id'] ?>">

    <div>
        <label>Nom</label><br>
        <input name="nom" value="<?= htmlspecialchars($allergene['nom']) ?>" required>
    </div>

    <button type="submit">Enregistrer</button>
</form>

<!-- Lien de retour vers la liste des allergènes -->
<p><a href="index.php?page=employe_allergenes">← Retour</a></p>

<?php require __DIR__ . '/../partials/footer.php'; ?>
