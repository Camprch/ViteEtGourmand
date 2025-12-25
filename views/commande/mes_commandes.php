<?php 
$pageTitle = 'Mes commandes';
require __DIR__ . '/../partials/header.php';
?>

<?php
function fr_datetime(?string $s): string {
    if (!$s) return '';
    $ts = strtotime($s);
    return $ts ? date('d/m/Y H:i', $ts) : htmlspecialchars($s);
}
function fr_date(?string $s): string {
    if (!$s) return '';
    $ts = strtotime($s);
    return $ts ? date('d/m/Y', $ts) : htmlspecialchars($s);
}
?>

<?php require_once __DIR__ . '/../../src/security/Csrf.php'; ?>

<h2>Mes commandes</h2>

<?php if (empty($commandes)): ?>
    <p>Vous n'avez pas encore passé de commande.</p>

<?php else: ?>
    <ul>
        <?php foreach ($commandes as $cmd): ?>
            <li>
                <strong>Commande n°<?= (int)$cmd['id'] ?></strong><br>
                Menu : <?= htmlspecialchars($cmd['menu_titre']) ?><br>
                Date commande : <?= fr_datetime($cmd['date_commande'] ?? null) ?><br>
                Prestation : <?= fr_date($cmd['date_prestation'] ?? null) ?><br>
                Montant total : <?= number_format((float)$cmd['prix_total'], 2, ',', ' ') ?> €<br>
                Statut : <strong><?= htmlspecialchars($cmd['statut_courant']) ?></strong><br>

                <a href="index.php?page=commande_detail&id=<?= (int)$cmd['id'] ?>">
                    Voir le détail
                </a>
            </li>
            <hr>
        <?php endforeach; ?>
    </ul>
<?php endif; ?>

<?php require __DIR__ . '/../partials/footer.php'; ?>
