<?php 

// Vue : liste des commandes passées par l'utilisateur connecté

$pageTitle = 'Mes commandes';
require __DIR__ . '/../partials/header.php';
?>

<?php require_once __DIR__ . '/../../src/security/Csrf.php'; ?>

<section class="page-head">
    <div>
        <p class="eyebrow">Suivi</p>
        <h2>Mes commandes</h2>
        <p class="muted">Retrouvez l’historique de vos prestations.</p>
    </div>
</section>

<?php if (empty($commandes)): ?>
    <p>Vous n'avez pas encore passé de commande.</p>

<?php else: ?>
    <section>
        <div class="cards-grid">
            <?php foreach ($commandes as $cmd): ?>
                <article class="card">
                    <p class="card-title">
                        Commande n°<?= (int)$cmd['id'] ?>
                        <span class="badge badge-outline"><?= htmlspecialchars($cmd['statut_courant']) ?></span>
                    </p>
                    <p><strong>Menu :</strong> <?= htmlspecialchars($cmd['menu_titre']) ?></p>
                    <p class="muted">Commande : <?= fr_datetime($cmd['date_commande'] ?? null) ?></p>
                    <p class="muted">Prestation : <?= fr_date($cmd['date_prestation'] ?? null) ?></p>
                    <p><strong>Total :</strong> <?= number_format((float)$cmd['prix_total'], 2, ',', ' ') ?> €</p>

                    <a class="btn btn-ghost" href="index.php?page=commande_detail&id=<?= (int)$cmd['id'] ?>">
                        Voir le détail
                    </a>
                </article>
            <?php endforeach; ?>
        </div>
    </section>
<?php endif; ?>

<?php require __DIR__ . '/../partials/footer.php'; ?>
