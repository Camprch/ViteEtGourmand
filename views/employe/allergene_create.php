
<?php

// Vue : formulaire de création d'un allergène

// Utilisé par : EmployeAllergeneController::create()

require __DIR__ . '/../partials/header.php';
?>

<!-- Titre de la page -->
<h1>Créer un allergène</h1>

<!-- Formulaire de création d'un allergène -->
<form method="post" action="index.php?page=employe_allergene_store">
    <!-- Protection CSRF -->
    <input type="hidden" name="_csrf" value="<?= htmlspecialchars(Csrf::token()) ?>">
    <div>
        <label>Nom</label><br>
        <input name="nom" required>
    </div>
    <button type="submit">Créer</button>
</form>

<!-- Lien de retour vers la liste des allergènes -->
<p><a href="index.php?page=employe_allergenes">← Retour</a></p>

<?php require __DIR__ . '/../partials/footer.php'; ?>
