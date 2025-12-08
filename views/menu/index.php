<?php
// views/menu/index.php
$pageTitle = 'Nos menus - Vite & Gourmand';
require __DIR__ . '/../partials/header.php';
?>

<h2>Nos menus</h2>

<?php if (empty($menus)): ?>
    <p>Aucun menu disponible pour le moment.</p>
<?php else: ?>
    <ul>
        <?php foreach ($menus as $menu): ?>
            <li>
                <h3><?= htmlspecialchars($menu['titre']) ?></h3>
                <p><?= nl2br(htmlspecialchars($menu['description'])) ?></p>
                <p>
                    Minimum <?= (int)$menu['personnes_min'] ?> personnes – 
                    <?= number_format((float)$menu['prix_par_personne'], 2, ',', ' ') ?> € / personne
                </p>
                <a href="index.php?page=menu&id=<?= (int)$menu['id'] ?>">Voir le détail</a>
            </li>
        <?php endforeach; ?>
    </ul>
<?php endif; ?>

<?php require __DIR__ . '/../partials/footer.php'; ?>
