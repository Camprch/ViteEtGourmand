<?php require __DIR__ . '/../partials/header.php'; ?>

<h1>Modifier un allergène</h1>

<form method="post" action="index.php?page=employe_allergene_update">
    <input type="hidden" name="_csrf" value="<?= htmlspecialchars(Csrf::token()) ?>">
    <input type="hidden" name="id" value="<?= (int)$allergene['id'] ?>">

    <div>
        <label>Nom</label><br>
        <input name="nom" value="<?= htmlspecialchars($allergene['nom']) ?>" required>
    </div>

    <button type="submit">Enregistrer</button>
</form>

<p><a href="index.php?page=employe_allergenes">← Retour</a></p>

<?php require __DIR__ . '/../partials/footer.php'; ?>
