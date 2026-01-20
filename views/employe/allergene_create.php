<?php

// Vue : formulaire de création d'un allergène

// Utilisé par : EmployeAllergeneController::create()

require __DIR__ . '/../partials/header.php';
?>

<section class="page-head">
    <div>
        <p class="eyebrow">Allergènes</p>
        <h1>Créer un allergène</h1>
    </div>
</section>

<section class="card">
<form method="post" action="index.php?page=employe_allergene_store" class="form-stack">
    <!-- Protection CSRF -->
    <input type="hidden" name="_csrf" value="<?= htmlspecialchars(Csrf::token()) ?>">
    <label>Nom
        <input name="nom" required>
    </label>
    <div class="form-actions">
        <button type="submit">Créer</button>
        <a class="btn btn-ghost" href="index.php?page=employe_allergenes">Retour</a>
    </div>
</form>
</section>

<?php require __DIR__ . '/../partials/footer.php'; ?>
