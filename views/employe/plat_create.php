<?php

// Vue : Création d'un plat pour les employés

// Utilisé par : EmployePlatController::create()
require __DIR__ . '/../partials/header.php';
?>


<section class="page-head">
    <div>
        <p class="eyebrow">Plats</p>
        <h1>Créer un plat</h1>
    </div>
</section>

<section class="card">
<form method="post" action="index.php?page=employe_plat_store" class="form-grid">
    <!-- Protection CSRF -->
    <input type="hidden" name="_csrf" value="<?= htmlspecialchars(Csrf::token()) ?>">

    <!-- Champs principaux du plat -->
    <label>Nom
        <input name="nom" required>
    </label>

    <label class="span-2">Description
        <textarea name="description"></textarea>
    </label>

    <label>Type
        <select name="type" required>
            <option value="ENTREE">ENTREE</option>
            <option value="PLAT">PLAT</option>
            <option value="DESSERT">DESSERT</option>
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
                <label>
                    <input type="checkbox" name="allergenes[]" value="<?= (int)$a['id'] ?>">
                    <?= htmlspecialchars($a['nom']) ?>
                </label>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <div class="form-actions span-2">
        <button type="submit">Créer</button>
        <a class="btn btn-ghost" href="index.php?page=employe_plats">Retour</a>
    </div>
</form>
</section>


<?php require __DIR__ . '/../partials/footer.php'; ?>
