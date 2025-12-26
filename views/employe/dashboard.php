<?php
$pageTitle = "Espace employé - Vite & Gourmand";
require __DIR__ . '/../partials/header.php';
?>

<h2>Espace employé</h2>

<ul>
    <li><a href="index.php?page=employe_commandes">Gérer les commandes (statuts, annulations)</a></li>
    <li><a href="index.php?page=employe_menus">Menus</a></li>
    <li><a href="index.php?page=employe_plats">Plats</a></li>
    <li><a href="index.php?page=employe_allergenes">Allergènes</a></li>
    <li><a href="index.php?page=employe_horaires">Gérer les horaires</a></li>
    <li><a href="index.php?page=avis_a_valider">Modérer les avis</a></li>
</ul>

<p><a href="index.php?page=home">Retour accueil</a></p>

<?php require __DIR__ . '/../partials/footer.php'; ?>