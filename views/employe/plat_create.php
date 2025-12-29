
<?php

// Vue : Création d'un plat pour les employés

// Utilisé par : EmployePlatController::create()
require __DIR__ . '/../partials/header.php';
?>


<!-- Titre de la page -->
<h1>Créer un plat</h1>


<!-- Formulaire de création d'un plat -->
<form method="post" action="index.php?page=employe_plat_store">
    <!-- Protection CSRF -->
    <input type="hidden" name="_csrf" value="<?= htmlspecialchars(Csrf::token()) ?>">

    <!-- Champs principaux du plat -->
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
    <!-- Section de sélection des allergènes associés au plat -->
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


<!-- Lien de retour vers la liste des plats -->
<p><a href="index.php?page=employe_plats">← Retour liste</a></p>


<?php require __DIR__ . '/../partials/footer.php'; ?>
