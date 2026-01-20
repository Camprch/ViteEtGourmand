<?php

// Vue : page d'accueil de l'administration (dashboard admin)

// Permet d'accéder à la gestion des employés et aux statistiques.

$pageTitle = "Administration - Vite & Gourmand";
require __DIR__ . '/../partials/header.php';
?>

<section class="page-head">
    <div>
        <p class="eyebrow">Administration</p>
        <h2>Tableau de bord</h2>
        <p class="muted">Pilotez les employés et les performances.</p>
    </div>
</section>

<section class="dashboard-grid">
    <div class="dashboard-card">
        <h3>Employés</h3>
        <p class="muted">Créez, activez ou désactivez les comptes.</p>
        <a class="btn" href="index.php?page=admin_employes">Gérer les employés</a>
    </div>
    <div class="dashboard-card">
        <h3>Statistiques</h3>
        <p class="muted">Analysez l’activité via MongoDB.</p>
        <a class="btn" href="index.php?page=admin_stats">Voir les stats</a>
    </div>
</section>

<section class="cta-bar">
    <a class="btn btn-ghost" href="index.php?page=home">Retour accueil</a>
</section>

<?php require __DIR__ . '/../partials/footer.php'; ?>
