<?php

// Vue : liste des avis en attente de validation (admin ou employé)

$pageTitle = 'Avis à valider';
require __DIR__ . '/../partials/header.php';
?>

<section class="page-head">
    <div>
        <p class="eyebrow">Modération</p>
        <h2>Avis à valider</h2>
        <p class="muted">Validez ou refusez les avis déposés par les clients.</p>
    </div>
</section>

<?php if (empty($avis)): ?>
    <p>Aucun avis en attente.</p>
<?php else: ?>
    <section>
        <div class="cards-grid">
            <?php foreach ($avis as $a): ?>
                <article class="card">
                    <p class="card-title">
                        <?= htmlspecialchars($a['prenom'] . ' ' . $a['nom']) ?>
                        <span class="badge">Note <?= (int)$a['note'] ?>/5</span>
                    </p>
                    <p class="muted">Menu : <?= htmlspecialchars($a['menu_titre']) ?></p>
                    <p><?= nl2br(htmlspecialchars($a['commentaire'])) ?></p>
                    <small class="muted"><?= htmlspecialchars($a['date']) ?></small>

                    <div class="action-row">
                        <form method="post" action="index.php?page=avis_valider">
                            <input type="hidden" name="_csrf" value="<?= htmlspecialchars(Csrf::token()) ?>">
                            <input type="hidden" name="avis_id" value="<?= (int)$a['id'] ?>">
                            <button class="btn-sm" type="submit">Valider</button>
                        </form>
                        
                        <form method="post" action="index.php?page=avis_refuser">
                            <input type="hidden" name="_csrf" value="<?= htmlspecialchars(Csrf::token()) ?>">
                            <input type="hidden" name="avis_id" value="<?= (int)$a['id'] ?>">
                            <button class="btn btn-ghost btn-sm" type="submit">Refuser</button>
                        </form>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>
    </section>
<?php endif; ?>

<?php
$user = $_SESSION['user'] ?? null;

$dashboard = $_SESSION['dashboard_context'] ?? (
    ($user && $user['role'] === 'ADMIN') ? 'dashboard_admin' : 'dashboard_employe'
);
?>

<section class="cta-bar">
    <a class="btn btn-ghost" href="index.php?page=<?= $dashboard ?>">Retour dashboard</a>
</section>

<?php require __DIR__ . '/../partials/footer.php'; ?>
