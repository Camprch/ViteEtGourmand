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
                <input type="hidden" name="avis_id" value="<?= (int)$a['id'] ?>">
                <button type="submit">Valider</button>
            </form>
        </div>
    <?php endforeach; ?>
<?php endif; ?>

<?php if (!empty($_SESSION['user']) && $_SESSION['user']['role'] === 'ADMIN'): ?>
    <p><a href="index.php?page=dashboard_admin">Retour administration</a></p>
<?php else: ?>
    <p><a href="index.php?page=dashboard_employe">Retour espace employé</a></p>
<?php endif; ?>

<?php require __DIR__ . '/../partials/footer.php'; ?>
