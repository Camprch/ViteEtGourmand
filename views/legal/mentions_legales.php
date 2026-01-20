<?php

// Vue : Mentions légales

// Utilisé par : route page=mentions_legales
$pageTitle = 'Mentions légales - Vite & Gourmand';
require __DIR__ . '/../partials/header.php';
?>


<section class="page-head">
    <div>
        <p class="eyebrow">Informations</p>
        <h2>Mentions légales</h2>
    </div>
</section>

<section class="card">
    <h3>Éditeur du site</h3>
    <p><strong>Vite & Gourmand</strong></p>
    <p>Adresse : Bordeaux (exemple)</p>
    <p>Email : contact@vite-gourmand.local</p>
    <p>Téléphone : 00 00 00 00 00</p>
</section>

<section class="card">
    <h3>Hébergement</h3>
    <p>Hébergeur : (à renseigner selon ton déploiement)</p>
</section>

<section class="card">
    <h3>Données personnelles</h3>
    <p>Les données collectées via le site (compte, commandes, contact) sont utilisées uniquement pour le fonctionnement du service. Vous pouvez demander la modification ou suppression de vos données via votre espace utilisateur ou en nous contactant.</p>
</section>


<?php require __DIR__ . '/../partials/footer.php'; ?>
