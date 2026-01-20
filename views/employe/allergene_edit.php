<?php

// Vue : formulaire d'édition d'un allergène

// Utilisé par : EmployeAllergeneController::edit()

require __DIR__ . '/../partials/header.php';
?>

<section class="page-head">
    <div>
        <p class="eyebrow">Allergènes</p>
        <h1>Modifier un allergène</h1>
    </div>
</section>

<section class="card">
<form method="post" action="index.php?page=employe_allergene_update" class="form-stack">
    <!-- Protection CSRF -->
    <input type="hidden" name="_csrf" value="<?= htmlspecialchars(Csrf::token()) ?>">
    <!-- Identifiant de l'allergène à modifier -->
    <input type="hidden" name="id" value="<?= (int)$allergene['id'] ?>">

    <label>Nom
        <input name="nom" value="<?= htmlspecialchars($allergene['nom']) ?>" required>
    </label>

    <div class="form-actions">
        <button type="submit">Enregistrer</button>
        <a class="btn btn-ghost" href="index.php?page=employe_allergenes">Retour</a>
    </div>
</form>
</section>

<?php require __DIR__ . '/../partials/footer.php'; ?>
