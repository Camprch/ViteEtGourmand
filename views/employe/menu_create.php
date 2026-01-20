<?php

// Vue : formulaire de création d'un menu

// Utilisé par : EmployeMenuController::create()
$pageTitle = "Ajouter un menu";
require __DIR__ . '/../partials/header.php';
?>

<section class="page-head">
    <div>
        <p class="eyebrow">Menus</p>
        <h2>Ajouter un menu</h2>
    </div>
</section>

<section class="card">
<form method="post" action="index.php?page=employe_menu_store" class="form-grid">

    <!-- Protection CSRF -->
    <?php require_once __DIR__ . '/../../src/security/Csrf.php'; ?>
    <input type="hidden" name="_csrf" value="<?= htmlspecialchars(Csrf::token()) ?>">
    
    <!-- Champs principaux du menu -->
    <label for="titre">Titre
        <input id="titre" name="titre" type="text" required>
    </label>

    <label class="span-2" for="description">Description
        <textarea id="description" name="description" rows="5" required></textarea>
    </label>

    <label for="theme">Thème (optionnel)
        <input id="theme" name="theme" type="text">
    </label>

    <label for="regime">Régime (optionnel)
        <input id="regime" name="regime" type="text">
    </label>

    <label for="prix_par_personne">Prix / personne (€)
        <input id="prix_par_personne" name="prix_par_personne" type="number" step="0.01" min="0.1" required>
    </label>

    <label for="personnes_min">Personnes minimum
        <input id="personnes_min" name="personnes_min" type="number" min="1" required>
    </label>

    <label class="span-2" for="conditions_particulieres">Conditions particulières (optionnel)
        <textarea id="conditions_particulieres" name="conditions_particulieres" rows="3"></textarea>
    </label>

    <label for="stock">Stock (optionnel)
        <input id="stock" name="stock" type="number" min="0">
    </label>

    <div class="form-actions span-2">
        <button type="submit">Créer</button>
        <a class="btn btn-ghost" href="index.php?page=employe_menus">Retour</a>
    </div>
</form>
</section>

<!-- Message d'information sur l'ajout d'images après création -->
<section class="notice"><em>Les images pourront être ajoutées une fois le menu créé.</em></section>

<?php require __DIR__ . '/../partials/footer.php'; ?>
