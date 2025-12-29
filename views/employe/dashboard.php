<?php
// Fichier : dashboard.php
// Rôle : Tableau de bord principal de l'espace employé
// Propose les liens vers les différentes fonctionnalités employé
$pageTitle = "Espace employé - Vite & Gourmand";
require __DIR__ . '/../partials/header.php';
?>

<!-- Titre de la page -->
<h2>Espace employé</h2>

<!-- Liste des liens vers les fonctionnalités principales pour l'employé -->
<ul>
    <li><a href="index.php?page=employe_commandes">Gérer les commandes (statuts, annulations)</a></li>
    <li><a href="index.php?page=employe_menus">Menus</a></li>
    <li><a href="index.php?page=employe_plats">Plats</a></li>
    <li><a href="index.php?page=employe_allergenes">Allergènes</a></li>
    <li><a href="index.php?page=employe_horaires">Gérer les horaires</a></li>
    <li><a href="index.php?page=avis_a_valider">Modérer les avis</a></li>
</ul>

<!-- Lien de retour vers l'accueil du site -->
<p><a href="index.php?page=home">Retour accueil</a></p>

<?php require __DIR__ . '/../partials/footer.php'; ?>