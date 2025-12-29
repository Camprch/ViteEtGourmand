
<?php

// Vue : Conditions Générales de Vente (CGV)

// Utilisé par : route page=cgv
$pageTitle = 'Conditions Générales de Vente - Vite & Gourmand';
require __DIR__ . '/../partials/header.php';
?>


<!-- Titre de la page -->
<h2>Conditions Générales de Vente (CGV)</h2>


<!-- Introduction -->
<p>Les présentes CGV définissent les conditions de commande des prestations de Vite & Gourmand.</p>


<!-- Bloc Commande -->
<h3>Commande</h3>
<p>La commande est confirmée après validation et peut évoluer selon le statut affiché dans l’espace utilisateur.</p>


<!-- Bloc Livraison -->
<h3>Livraison</h3>
<p>Des frais de livraison peuvent s’appliquer selon la localisation (voir récapitulatif au moment de la commande).</p>


<!-- Bloc Annulation -->
<h3>Annulation</h3>
<p>L’annulation est possible tant que la commande n’a pas été acceptée. Au-delà, contactez l’entreprise.</p>


<!-- Bloc Matériel prêté -->
<h3>Matériel prêté</h3>
<p>En cas de statut “Attente retour matériel”, le matériel doit être restitué sous 10 jours ouvrés. À défaut, des frais peuvent être appliqués (ex : 600 €).</p>


<?php require __DIR__ . '/../partials/footer.php'; ?>
