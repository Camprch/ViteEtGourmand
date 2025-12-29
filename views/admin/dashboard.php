<?php

// Vue : page d'accueil de l'administration (dashboard admin)

// Permet d'accéder à la gestion des employés et aux statistiques.

$pageTitle = "Administration - Vite & Gourmand";
require __DIR__ . '/../partials/header.php';
?>

<h2>Administration</h2>

<ul>
    <li><a href="index.php?page=admin_employes">Gérer les employés</a></li>
    <li><a href="index.php?page=admin_stats">Statistiques (MongoDB + graphique)</a></li>
</ul>

<p><a href="index.php?page=home">Retour accueil</a></p>

<?php require __DIR__ . '/../partials/footer.php'; ?>
