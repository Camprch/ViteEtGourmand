<?php
$pageTitle = 'Avis à valider';
require __DIR__ . '/../partials/header.php';
?>

<h2>Avis à valider</h2>

<?php if (empty($avis)): ?>
    <p>Aucun avis en attente.</p>
<?php else: ?>
    <?php foreach ($avis as $a): ?>
        <div style="border:1px solid #ccc; padding:10px; margin-bottom:10px;">
            <p><strong><?= htmlspecialchars($a['prenom'] . ' ' . $a['nom']) ?></strong>
                — Menu : <?= htmlspecialchars($a['menu_titre']) ?></p>
            <p>Note : <?= (int)$a['note'] ?>/5</p>
            <p><?= nl2br(htmlspecialchars($a['commentaire'])) ?></p>
            <small><?= htmlspecialchars($a['date']) ?></small>

            <form method="post" action="index.php?page=avis_valider">
                <input type="hidden" name="_csrf" value="<?= htmlspecialchars(Csrf::token()) ?>">
                <input type="hidden" name="avis_id" value="<?= (int)$a['id'] ?>">
                <button type="submit">Valider</button>
            </form>
            
            <form method="post" action="index.php?page=avis_refuser">
                <input type="hidden" name="_csrf" value="<?= htmlspecialchars(Csrf::token()) ?>">
                <input type="hidden" name="avis_id" value="<?= (int)$a['id'] ?>">
                <button type="submit">Refuser</button>
            </form>
        </div>
    <?php endforeach; ?>
<?php endif; ?>

<?php
$user = $_SESSION['user'] ?? null;

$dashboard = $_SESSION['dashboard_context'] ?? (
    ($user && $user['role'] === 'ADMIN') ? 'dashboard_admin' : 'dashboard_employe'
);
?>

<p><a href="index.php?page=<?= $dashboard ?>">Retour dashboard</a></p>

<?php require __DIR__ . '/../partials/footer.php'; ?>
