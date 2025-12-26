<?php
$pageTitle = "Ajouter un menu";
require __DIR__ . '/../partials/header.php';
?>

<h2>Ajouter un menu</h2>

<form method="post" action="index.php?page=employe_menu_store">

    <?php require_once __DIR__ . '/../../src/security/Csrf.php'; ?>
    <input type="hidden" name="_csrf" value="<?= htmlspecialchars(Csrf::token()) ?>">
    
    <label for="titre">Titre</label>
    <input id="titre" name="titre" type="text" required>

    <label for="description">Description</label>
    <textarea id="description" name="description" rows="5" required></textarea>

    <label for="theme">Thème (optionnel)</label>
    <input id="theme" name="theme" type="text">

    <label for="regime">Régime (optionnel)</label>
    <input id="regime" name="regime" type="text">

    <label for="prix_par_personne">Prix / personne (€)</label>
    <input id="prix_par_personne" name="prix_par_personne" type="number" step="0.01" min="0.1" required>

    <label for="personnes_min">Personnes minimum</label>
    <input id="personnes_min" name="personnes_min" type="number" min="1" required>

    <label for="conditions_particulieres">Conditions particulières (optionnel)</label>
    <textarea id="conditions_particulieres" name="conditions_particulieres" rows="3"></textarea>

    <label for="stock">Stock (optionnel)</label>
    <input id="stock" name="stock" type="number" min="0">

    <button type="submit">Créer</button>
</form>

<p><em>Les images pourront être ajoutées une fois le menu créé.</em></p>

<p><a href="index.php?page=dashboard_employe">Retour dashboard</a></p>

<?php require __DIR__ . '/../partials/footer.php'; ?>
