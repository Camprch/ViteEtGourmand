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

<?php if ($commande['statut_courant'] === 'EN_ATTENTE'): ?>
    <form method="post" action="index.php?page=annuler_commande" onsubmit="return confirm('Voulez-vous vraiment annuler cette commande ?');">
        <input type="hidden" name="id_commande" value="<?= (int)$commande['id'] ?>">
        <button type="submit">Annuler la commande</button>
    </form>
<?php endif; ?>

<h3>Historique des statuts</h3>

<?php if (empty($historiqueStatuts)): ?>
    <p>Aucun historique disponible.</p>
<?php else: ?>
    <ul>
        <?php foreach ($historiqueStatuts as $h): ?>
            <li>
                <?= htmlspecialchars($h['statut']) ?>
                — <?= htmlspecialchars($h['date_heure']) ?>
            </li>
        <?php endforeach; ?>
    </ul>
<?php endif; ?>

<?php if ($commande['statut_courant'] === 'TERMINEE'): ?>
    <h3>Laisser un avis</h3>

    <form method="post" action="index.php?page=avis_post">
        <input type="hidden" name="id_commande" value="<?= (int)$commande['id'] ?>">

        <label>Note (1 à 5) :</label>
        <input type="number" name="note" min="1" max="5" required>

        <label>Commentaire :</label>
        <textarea name="commentaire" required></textarea>

        <br><br>
        <button type="submit">Envoyer mon avis</button>
    </form>
<?php endif; ?>

<p>
    <a href="index.php?page=mes_commandes">← Retour à mes commandes</a>
</p>

<?php require __DIR__ . '/../partials/footer.php'; ?>