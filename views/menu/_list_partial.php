<?php
declare(strict_types=1);

/** @var array $menus */
?>

<?php if (empty($menus)): ?>
    <p>Aucun menu ne correspond à vos filtres.</p>
<?php else: ?>
    <?php foreach ($menus as $menu): ?>
        <article class="menu-card">
            <h3><?= htmlspecialchars((string)$menu['titre']) ?></h3>
            
            <?php if (array_key_exists('stock', $menu) && $menu['stock'] !== null && (int)$menu['stock'] <= 0): ?>
                <p><strong>Indisponible (rupture)</strong></p>
            <?php endif; ?>

            <p><?= nl2br(htmlspecialchars((string)$menu['description'])) ?></p>

            <p>
                <strong>Minimum :</strong> <?= (int)$menu['personnes_min'] ?> personnes
                — <strong>Prix / personne :</strong> <?= number_format((float)$menu['prix_par_personne'], 2, ',', ' ') ?> €
            </p>

            <a class="btn" href="index.php?page=menu&id=<?= (int)$menu['id'] ?>">Voir le détail</a>
        </article>
    <?php endforeach; ?>
<?php endif; ?>
