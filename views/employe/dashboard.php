<?php

// Vue : Tableau de bord employé

// Propose les liens vers les différentes fonctionnalités employé
$pageTitle = "Espace employé - Vite & Gourmand";
require __DIR__ . '/../partials/header.php';
?>

<section class="page-head">
    <div>
        <p class="eyebrow">Espace employé</p>
        <h2>Tableau de bord</h2>
        <p class="muted">Accès rapide aux opérations quotidiennes.</p>
    </div>
</section>

<section class="dashboard-grid">
    <div class="dashboard-card">
        <h3>Commandes</h3>
        <p class="muted">Mettre à jour les statuts et annuler si besoin.</p>
        <a class="btn" href="index.php?page=employe_commandes">Gérer les commandes</a>
    </div>
    <div class="dashboard-card">
        <h3>Menus</h3>
        <p class="muted">Créer, modifier, gérer les stocks et images.</p>
        <a class="btn" href="index.php?page=employe_menus">Menus</a>
    </div>
    <div class="dashboard-card">
        <h3>Plats</h3>
        <p class="muted">Gérer les plats et leurs allergènes.</p>
        <a class="btn" href="index.php?page=employe_plats">Plats</a>
    </div>
    <div class="dashboard-card">
        <h3>Allergènes</h3>
        <p class="muted">Référentiel des allergènes.</p>
        <a class="btn" href="index.php?page=employe_allergenes">Allergènes</a>
    </div>
    <div class="dashboard-card">
        <h3>Horaires</h3>
        <p class="muted">Mise à jour des horaires affichés.</p>
        <a class="btn" href="index.php?page=employe_horaires">Horaires</a>
    </div>
    <div class="dashboard-card">
        <h3>Avis clients</h3>
        <p class="muted">Modération des avis en attente.</p>
        <a class="btn" href="index.php?page=avis_a_valider">Modérer les avis</a>
    </div>
</section>

<section class="cta-bar">
    <a class="btn btn-ghost" href="index.php?page=home">Retour accueil</a>
</section>

<?php require __DIR__ . '/../partials/footer.php'; ?>
