<?php require __DIR__ . '/../partials/header.php'; ?>

<h1>Créer un allergène</h1>

<form method="post" action="index.php?page=employe_allergene_store">
    <input type="hidden" name="_csrf" value="<?= htmlspecialchars(Csrf::token()) ?>">
    <div>
        <label>Nom</label><br>
        <input name="nom" required>
    </div>
    <button type="submit">Créer</button>
</form>

<p><a href="index.php?page=employe_allergenes">← Retour</a></p>

<?php require __DIR__ . '/../partials/footer.php'; ?>
