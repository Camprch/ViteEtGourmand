<?php
// $menu est fourni par le controller
$pageTitle = "Commander : " . htmlspecialchars($menu['titre']);
require __DIR__ . '/../partials/header.php';
?>

<!-- Vue : formulaire de création d'une commande pour un menu donné -->

<h2>Commander : <?= htmlspecialchars($menu['titre']) ?></h2>

<p><strong>Minimum :</strong> <?= (int)$menu['personnes_min'] ?> personnes</p>
<p><strong>Prix/personne :</strong>
    <?= number_format((float)$menu['prix_par_personne'], 2, ',', ' ') ?> €
</p>

<?php require_once __DIR__ . '/../../src/security/Csrf.php'; ?>

<form method="post" action="index.php?page=commande_traitement">

    <input type="hidden" name="id_menu" value="<?= (int)$menu['id'] ?>">
    <input type="hidden" name="_csrf" value="<?= htmlspecialchars(Csrf::token()) ?>">

    <label>Nombre de personnes :</label>
    <input type="number" name="nb_personnes" min="<?= (int)$menu['personnes_min'] ?>" required>

    <label>Date de prestation :</label>
    <input type="date" name="date_prestation" required>

    <label>Heure de prestation :</label>
    <input type="time" name="heure_prestation" required>

    <label>Adresse de prestation :</label>
    <input type="text" name="adresse_prestation" required>

    <label>Ville :</label>
    <input type="text" name="ville" required>

    <label>Code postal :</label>
    <input type="text" name="code_postal" required>

    <label>Distance depuis Bordeaux (en km) :</label>
    <input type="number" name="distance_km" min="0" step="0.1" value="0">

    <br><br>
    <button type="submit">Valider la commande</button>
</form>

<?php require __DIR__ . '/../partials/footer.php'; ?>
