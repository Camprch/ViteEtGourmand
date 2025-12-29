<?php

// Vue : édition d'un plat

// Utilisé par : EmployePlatController::edit()
require __DIR__ . '/../partials/header.php';
?>


<!-- Titre de la page -->
<h1>Modifier un plat</h1>


<!-- Formulaire d'édition d'un plat -->
<form method="post" action="index.php?page=employe_plat_update">
    <!-- Protection CSRF et identifiant du plat -->
    <input type="hidden" name="_csrf" value="<?= htmlspecialchars(Csrf::token()) ?>">
    <input type="hidden" name="id" value="<?= (int)$plat['id'] ?>">

    <!-- Champs principaux du plat -->
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

    <hr>
    <!-- Section de sélection des allergènes associés au plat -->
    <h2>Allergènes</h2>

    <?php if (empty($allergenes)): ?>
        <p>Aucun allergène créé pour le moment.</p>
    <?php else: ?>
        <?php foreach ($allergenes as $a): ?>
            <?php $aid = (int)$a['id']; ?>
            <label style="display:block; margin:4px 0;">
                <input type="checkbox"
                    name="allergenes[]"
                    value="<?= $aid ?>"
                    <?= isset($platAllergeneIds[$aid]) ? 'checked' : '' ?>>
                <?= htmlspecialchars($a['nom']) ?>
            </label>
        <?php endforeach; ?>
    <?php endif; ?>

    <button type="submit">Enregistrer</button>
</form>


<!-- Lien de retour vers la liste des plats -->
<p><a href="index.php?page=employe_plats">← Retour liste</a></p>


<?php require __DIR__ . '/../partials/footer.php'; ?>
