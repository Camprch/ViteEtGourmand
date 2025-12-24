<?php
$pageTitle = "Espace employé - Vite & Gourmand";
require __DIR__ . '/../partials/header.php';
?>

<h2>Espace employé</h2>

<ul>
    <li><a href="index.php?page=employe_commandes">Gérer les commandes (statuts, annulations)</a></li>
    <li><a href="index.php?page=avis_a_valider">Modérer les avis</a></li>
    <li><a href="index.php?page=employe_menu_create">Ajouter un menu</a></li>
</ul>

<p><a href="index.php?page=home">Retour dashboard</a></p>

<?php require __DIR__ . '/../partials/footer.php'; ?>