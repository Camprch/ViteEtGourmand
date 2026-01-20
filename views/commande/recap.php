<?php

// Vue : récapitulatif d'une commande validée (affichage des infos et confirmation)

// Variables attendues : $menu, $nbPersonnes, $prixParPersonne, $prixMenuTotal, $reduction, $fraisLivraison, $prixTotal, $commandeId,
// $datePrestation, $heurePrestation, $adresse, $ville, $codePostal
// Vue : récapitulatif d'une commande validée (affichage des infos et confirmation) -->

$pageTitle = "Récap commande #" . (int)$commandeId;
require __DIR__ . '/../partials/header.php';
?>

<section class="page-head">
    <div>
        <p class="eyebrow">Confirmation</p>
        <h2>Récapitulatif de votre commande</h2>
        <p class="muted">Commande n°<?= (int)$commandeId ?> enregistrée.</p>
    </div>
</section>

<section class="order-detail">
    <div class="card">
        <h3>Menu choisi</h3>
        <p><strong><?= htmlspecialchars((string)$menu['titre']) ?></strong></p>
        <p class="muted">
            Prestation le <?= htmlspecialchars((string)$datePrestation) ?>
            à <?= htmlspecialchars((string)$heurePrestation) ?>
        </p>
        <p class="muted">
            Adresse : <?= htmlspecialchars((string)$adresse) ?>,
            <?= htmlspecialchars((string)$codePostal) ?>
            <?= htmlspecialchars((string)$ville) ?>
        </p>
    </div>

    <div class="card">
        <h3>Récapitulatif</h3>
        <ul class="recap-list">
            <li><span>Nombre de personnes</span><strong><?= (int)$nbPersonnes ?></strong></li>
            <li><span>Prix par personne</span><strong><?= number_format((float)$prixParPersonne, 2, ',', ' ') ?> €</strong></li>
            <li><span>Total menus</span><strong><?= number_format((float)$prixMenuTotal, 2, ',', ' ') ?> €</strong></li>
            <li><span>Réduction</span><strong><?= number_format((float)$reduction, 2, ',', ' ') ?> €</strong></li>
            <li><span>Frais de livraison</span><strong><?= number_format((float)$fraisLivraison, 2, ',', ' ') ?> €</strong></li>
            <li><span>Prix total</span><strong><?= number_format((float)$prixTotal, 2, ',', ' ') ?> €</strong></li>
        </ul>
    </div>
</section>

<section class="cta-bar">
    <a class="btn btn-ghost" href="index.php?page=menus">← Retour aux menus</a>
</section>

<?php require __DIR__ . '/../partials/footer.php'; ?>
