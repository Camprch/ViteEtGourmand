<?php
$pageTitle = 'Détail commande n°' . (int)$commande['id'];
require __DIR__ . '/../partials/header.php';
?>

<h2>Commande n°<?= (int)$commande['id'] ?></h2>

<p><strong>Menu :</strong> <?= htmlspecialchars($commande['menu_titre']) ?></p>
<p><strong>Date commande :</strong> <?= htmlspecialchars($commande['date_commande']) ?></p>
<p><strong>Date prestation :</strong> <?= htmlspecialchars($commande['date_prestation']) ?>
    à <?= htmlspecialchars($commande['heure_prestation']) ?></p>

<p><strong>Adresse :</strong>
    <?= htmlspecialchars($commande['adresse_prestation']) ?>,
    <?= htmlspecialchars($commande['code_postal']) ?>
    <?= htmlspecialchars($commande['ville']) ?>
</p>

<p><strong>Nombre de personnes :</strong> <?= (int)$commande['nb_personnes'] ?></p>

<p><strong>Total menus :</strong>
    <?= number_format((float)$commande['prix_menu_total'], 2, ',', ' ') ?> €</p>
<p><strong>Réduction appliquée :</strong>
    <?= number_format((float)$commande['reduction_appliquee'], 2, ',', ' ') ?> €</p>
<p><strong>Frais de livraison :</strong>
    <?= number_format((float)$commande['frais_livraison'], 2, ',', ' ') ?> €</p>
<p><strong>Prix total :</strong>
    <?= number_format((float)$commande['prix_total'], 2, ',', ' ') ?> €</p>

<p><strong>Statut actuel :</strong>
    <?= htmlspecialchars($commande['statut_courant']) ?></p>

<p>
    <a href="index.php?page=mes_commandes">← Retour à mes commandes</a>
</p>

<?php require __DIR__ . '/../partials/footer.php'; ?>