<?php require __DIR__ . '/../partials/header.php'; ?>

<h1>Créer un plat</h1>

<form method="post" action="index.php?page=employe_plat_store">
    <input type="hidden" name="_csrf" value="<?= htmlspecialchars(Csrf::token()) ?>">

    <div>
        <label>Nom</label><br>
        <input name="nom" required>
    </div>

    <div>
        <label>Description</label><br>
        <textarea name="description"></textarea>
    </div>

    <div>
        <label>Type</label><br>
        <select name="type" required>
            <option value="ENTREE">ENTREE</option>
            <option value="PLAT">PLAT</option>
            <option value="DESSERT">DESSERT</option>
        </select>
    </div>

    <hr>
    <h2>Allergènes</h2>

    <?php if (empty($allergenes)): ?>
        <p>Aucun allergène créé pour le moment.</p>
    <?php else: ?>
        <?php foreach ($allergenes as $a): ?>
            <label style="display:block; margin:4px 0;">
                <input type="checkbox" name="allergenes[]" value="<?= (int)$a['id'] ?>">
                <?= htmlspecialchars($a['nom']) ?>
            </label>
        <?php endforeach; ?>
    <?php endif; ?>

    <button type="submit">Créer</button>
</form>

<p><a href="index.php?page=employe_plats">← Retour liste</a></p>

<?php require __DIR__ . '/../partials/footer.php'; ?>
