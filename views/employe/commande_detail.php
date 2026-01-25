<?php

// Vue : détail d'une commande côté employé (avec historique)

$pageTitle = 'Commande n°' . (int)$commande['id'] . ' - Employé';
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
        <h3>Détails client</h3>
        <p><strong>Client :</strong> <?= htmlspecialchars(trim(($commande['prenom'] ?? '') . ' ' . ($commande['nom'] ?? ''))) ?></p>
        <p class="muted">Email : <?= htmlspecialchars((string)($commande['email'] ?? '')) ?></p>
        <p class="muted">Téléphone : <?= htmlspecialchars((string)($commande['telephone'] ?? '')) ?></p>
    </div>

    <div class="card">
        <h3>Détails commande</h3>
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
    </div>
</section>

<section>
    <h3>Historique des statuts</h3>

    <?php if (empty($historiqueStatuts)): ?>
        <p>Aucun historique disponible.</p>
    <?php else: ?>
        <ul class="timeline">
            <?php foreach ($historiqueStatuts as $h): ?>
                <?php
                    $origine = !empty($h['id_employe'])
                        ? 'Employé #' . (int)$h['id_employe']
                        : 'Client / Système';
                ?>
                <li>
                    <strong><?= htmlspecialchars($h['statut']) ?></strong>
                    <span class="muted">— <?= fr_datetime($h['date_heure'] ?? null) ?></span>
                    <div class="muted">Origine : <?= htmlspecialchars($origine) ?></div>
                    <?php if (!empty($h['commentaire'])): ?>
                        <div class="muted"><?= htmlspecialchars($h['commentaire']) ?></div>
                    <?php endif; ?>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>
</section>

<section class="cta-bar">
    <a class="btn btn-ghost" href="index.php?page=employe_commandes">← Retour aux commandes</a>
</section>

<?php require __DIR__ . '/../partials/footer.php'; ?>
