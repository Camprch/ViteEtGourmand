<?php require __DIR__ . '/../partials/header.php'; ?>

<h1>Modifier un plat</h1>

<form method="post" action="index.php?page=employe_plat_update">
    <input type="hidden" name="_csrf" value="<?= htmlspecialchars(Csrf::token()) ?>">
    <input type="hidden" name="id" value="<?= (int)$plat['id'] ?>">

    <div>
        <label>Nom</label><br>
        <input name="nom" value="<?= htmlspecialchars($plat['nom']) ?>" required>
    </div>

    <div>
        <label>Description</label><br>
        <textarea name="description"><?= htmlspecialchars($plat['description'] ?? '') ?></textarea>
    </div>

    <div>
        <label>Type</label><br>
        <select name="type" required>
            <?php foreach (['ENTREE','PLAT','DESSERT'] as $t): ?>
                <option value="<?= $t ?>" <?= $plat['type'] === $t ? 'selected' : '' ?>><?= $t ?></option>
            <?php endforeach; ?>
        </select>
    </div>

    <button type="submit">Enregistrer</button>
</form>

<p><a href="index.php?page=employe_plats">← Retour liste</a></p>

<?php require __DIR__ . '/../partials/footer.php'; ?>
