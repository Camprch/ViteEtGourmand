<?php
$pageTitle = "Administration - Vite & Gourmand";
require __DIR__ . '/../partials/header.php';
?>

<h2>Administration</h2>

<ul>
    <li><a href="index.php?page=employe_commandes">Gérer les commandes (statuts, annulations)</a></li>
    <li><a href="index.php?page=avis_a_valider">Modérer les avis</a></li>
    <li><a href="index.php?page=admin_employes">Gérer les employés</a></li>
    <li><a href="index.php?page=admin_stats">Statistiques (MongoDB + graphique)</a></li>
    <li><a href="index.php?page=employe_menu_create">Ajouter un menu</a></li>
    <li><a href="index.php?page=employe_horaires">Gérer les horaires</a></li>
</ul>

<p><a href="index.php?page=home">Retour accueil</a></p>

<?php require __DIR__ . '/../partials/footer.php'; ?>
