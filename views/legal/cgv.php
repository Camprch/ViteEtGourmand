<?php

// Vue : Conditions Générales de Vente (CGV)

// Utilisé par : route page=cgv
$pageTitle = 'Conditions Générales de Vente - Vite & Gourmand';
require __DIR__ . '/../partials/header.php';
?>


<section class="page-head">
    <div>
        <p class="eyebrow">Informations</p>
        <h2>Conditions Générales de Vente (CGV)</h2>
    </div>
</section>

<section class="card">
    <p>Les présentes CGV définissent les conditions de commande des prestations de Vite & Gourmand.</p>
</section>

<section class="card">
    <h3>Commande</h3>
    <p>La commande est confirmée après validation et peut évoluer selon le statut affiché dans l’espace utilisateur.</p>
</section>

<section class="card">
    <h3>Livraison</h3>
    <p>Des frais de livraison peuvent s’appliquer selon la localisation (voir récapitulatif au moment de la commande).</p>
</section>

<section class="card">
    <h3>Annulation</h3>
    <p>L’annulation est possible tant que la commande n’a pas été acceptée. Au-delà, contactez l’entreprise.</p>
</section>

<section class="card">
    <h3>Matériel prêté</h3>
    <p>En cas de statut “Attente retour matériel”, le matériel doit être restitué sous 10 jours ouvrés. À défaut, des frais peuvent être appliqués (ex : 600 €).</p>
</section>


<?php require __DIR__ . '/../partials/footer.php'; ?>
