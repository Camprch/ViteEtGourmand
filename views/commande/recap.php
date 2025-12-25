<?php
// views/commande/recap.php
// Variables attendues : $menu, $nbPersonnes, $prixParPersonne, $prixMenuTotal, $reduction, $fraisLivraison, $prixTotal, $commandeId,
// $datePrestation, $heurePrestation, $adresse, $ville, $codePostal

$pageTitle = "Récap commande #" . (int)$commandeId;
require __DIR__ . '/../partials/header.php';
?>

<h2>Récapitulatif de votre commande</h2>

<p>Menu : <strong><?= htmlspecialchars((string)$menu['titre']) ?></strong></p>

<ul>
    <li>Nombre de personnes : <?= (int)$nbPersonnes ?></li>
    <li>Prix par personne : <?= number_format((float)$prixParPersonne, 2, ',', ' ') ?> €</li>
    <li>Total menus : <strong><?= number_format((float)$prixMenuTotal, 2, ',', ' ') ?> €</strong></li>
    <li>Réduction : <?= number_format((float)$reduction, 2, ',', ' ') ?> €</li>
    <li>Frais de livraison : <?= number_format((float)$fraisLivraison, 2, ',', ' ') ?> €</li>
    <li>Prix total : <strong><?= number_format((float)$prixTotal, 2, ',', ' ') ?> €</strong></li>
</ul>

<p>Commande n° <strong><?= (int)$commandeId ?></strong> enregistrée.</p>

<hr>

<p>
    Prestation le <?= htmlspecialchars((string)$datePrestation) ?>
    à <?= htmlspecialchars((string)$heurePrestation) ?>
</p>

<p>
    Adresse : <?= htmlspecialchars((string)$adresse) ?>,
    <?= htmlspecialchars((string)$codePostal) ?>
    <?= htmlspecialchars((string)$ville) ?>
</p>

<p><a href="index.php?page=menus">← Retour aux menus</a></p>

<?php require __DIR__ . '/../partials/footer.php'; ?>
