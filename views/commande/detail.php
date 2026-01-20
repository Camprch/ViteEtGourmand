<?php

// Vue : affiche le détail d'une commande (infos, historique, actions)

$pageTitle = 'Détail commande n°' . (int)$commande['id'];
require __DIR__ . '/../partials/header.php';
?>

<section class="page-head">
    <div>
        <p class="eyebrow">Commande</p>
        <h2>Commande n°<?= (int)$commande['id'] ?></h2>
        <p class="muted">Statut actuel : <?= htmlspecialchars($commande['statut_courant']) ?></p>
    </div>
</section>

<section class="order-detail">
    <div class="card">
        <h3>Détails</h3>
        <p><strong>Menu :</strong> <?= htmlspecialchars($commande['menu_titre']) ?></p>
        <p class="muted">Commande : <?= fr_datetime($commande['date_commande'] ?? null) ?></p>
        <p class="muted">Prestation : <?= fr_date($commande['date_prestation'] ?? null) ?>
            à <?= htmlspecialchars((string)($commande['heure_prestation'] ?? '')) ?></p>

        <p><strong>Adresse :</strong>
            <?= htmlspecialchars($commande['adresse_prestation']) ?>,
            <?= htmlspecialchars($commande['code_postal']) ?>
            <?= htmlspecialchars($commande['ville']) ?>
        </p>

        <p><strong>Nombre de personnes :</strong> <?= (int)$commande['nb_personnes'] ?></p>
    </div>

    <div class="card">
        <h3>Récapitulatif</h3>
        <p><strong>Total menus :</strong>
            <?= number_format((float)$commande['prix_menu_total'], 2, ',', ' ') ?> €</p>
        <p><strong>Réduction :</strong>
            <?= number_format((float)$commande['reduction_appliquee'], 2, ',', ' ') ?> €</p>
        <p><strong>Frais de livraison :</strong>
            <?= number_format((float)$commande['frais_livraison'], 2, ',', ' ') ?> €</p>
        <p class="card-title">Prix total :
            <?= number_format((float)$commande['prix_total'], 2, ',', ' ') ?> €</p>

        <?php if ($commande['statut_courant'] === 'EN_ATTENTE'): ?>
            <form method="post" action="index.php?page=annuler_commande" onsubmit="return confirm('Voulez-vous vraiment annuler cette commande ?');" class="form-actions">
                <input type="hidden" name="id_commande" value="<?= (int)$commande['id'] ?>">
                <input type="hidden" name="_csrf" value="<?= htmlspecialchars(Csrf::token()) ?>">
                <button type="submit">Annuler la commande</button>
            </form>
        <?php endif; ?>
    </div>
</section>

<section>
    <h3>Historique des statuts</h3>
    <p class="muted">Règle d’annulation : possible uniquement tant que la commande est <strong>EN_ATTENTE</strong>.</p>

    <?php if (empty($historiqueStatuts)): ?>
        <p>Aucun historique disponible.</p>
    <?php else: ?>
        <ul class="timeline">
            <?php foreach ($historiqueStatuts as $h): ?>
                <li>
                    <strong><?= htmlspecialchars($h['statut']) ?></strong>
                    <span class="muted">— <?= fr_datetime($h['date_heure'] ?? null) ?></span>
                    <?php if (!empty($h['commentaire'])): ?>
                        <div class="muted"><?= htmlspecialchars($h['commentaire']) ?></div>
                    <?php endif; ?>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>
</section>

<?php if ($commande['statut_courant'] === 'TERMINEE'): ?>
    <section class="card">
        <h3>Laisser un avis</h3>

        <form method="post" action="index.php?page=avis_post" class="form-stack">
            <input type="hidden" name="id_commande" value="<?= (int)$commande['id'] ?>">
            <input type="hidden" name="id_menu" value="<?= (int)$commande['id_menu'] ?>">
            <input type="hidden" name="_csrf" value="<?= htmlspecialchars(Csrf::token()) ?>">

            <label>Note (1 à 5)
                <input type="number" name="note" min="1" max="5" required>
            </label>

            <label>Commentaire
                <textarea name="commentaire" required></textarea>
            </label>

            <button type="submit">Envoyer mon avis</button>
        </form>
    </section>
<?php endif; ?>

<section class="cta-bar">
    <a class="btn btn-ghost" href="index.php?page=mes_commandes">← Retour à mes commandes</a>
</section>

<?php require __DIR__ . '/../partials/footer.php'; ?>
