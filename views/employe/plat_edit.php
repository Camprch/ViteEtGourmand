<?php

// Vue : édition d'un plat

// Utilisé par : EmployePlatController::edit()
require __DIR__ . '/../partials/header.php';
?>


<section class="page-head">
    <div>
        <p class="eyebrow">Plats</p>
        <h1>Modifier un plat</h1>
    </div>
</section>

<section class="card">
<form method="post" action="index.php?page=employe_plat_update" class="form-grid">
    <!-- Protection CSRF et identifiant du plat -->
    <input type="hidden" name="_csrf" value="<?= htmlspecialchars(Csrf::token()) ?>">
    <input type="hidden" name="id" value="<?= (int)$plat['id'] ?>">

    <!-- Champs principaux du plat -->
    <label>Nom
        <input name="nom" value="<?= htmlspecialchars($plat['nom']) ?>" required>
    </label>

    <label class="span-2">Description
        <textarea name="description"><?= htmlspecialchars($plat['description'] ?? '') ?></textarea>
    </label>

    <label>Type
        <select name="type" required>
            <?php foreach (['ENTREE','PLAT','DESSERT'] as $t): ?>
                <option value="<?= $t ?>" <?= $plat['type'] === $t ? 'selected' : '' ?>><?= $t ?></option>
            <?php endforeach; ?>
        </select>
    </label>

    <hr>
    <!-- Section de sélection des allergènes associés au plat -->
    <h2 class="span-2">Allergènes</h2>

    <?php if (empty($allergenes)): ?>
        <p>Aucun allergène créé pour le moment.</p>
    <?php else: ?>
        <div class="stack span-2">
            <?php foreach ($allergenes as $a): ?>
                <?php $aid = (int)$a['id']; ?>
                <label>
                    <input type="checkbox"
                        name="allergenes[]"
                        value="<?= $aid ?>"
                        <?= isset($platAllergeneIds[$aid]) ? 'checked' : '' ?>>
                    <?= htmlspecialchars($a['nom']) ?>
                </label>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <div class="form-actions span-2">
        <button type="submit">Enregistrer</button>
        <a class="btn btn-ghost" href="index.php?page=employe_plats">Retour</a>
    </div>
</form>
</section>


<?php require __DIR__ . '/../partials/footer.php'; ?>
