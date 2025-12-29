
<?php

// Vue : liste partielle des menus

// Utilisé par : views/menu/index.php (affichage initial et AJAX)
declare(strict_types=1);

/** @var array $menus */
?>


<!-- Affichage si aucun menu ne correspond aux filtres -->
<?php if (empty($menus)): ?>
    <p>Aucun menu ne correspond à vos filtres.</p>
<?php else: ?>
    <!-- Boucle sur chaque menu pour affichage sous forme de carte -->
    <?php foreach ($menus as $menu): ?>
        <article class="menu-card">
            <h3><?= htmlspecialchars((string)$menu['titre']) ?></h3>
            
            <!-- Indication de rupture de stock -->
            <?php if (array_key_exists('stock', $menu) && $menu['stock'] !== null && (int)$menu['stock'] <= 0): ?>
                <p><strong>Indisponible (rupture)</strong></p>
            <?php endif; ?>

            <p><?= nl2br(htmlspecialchars((string)$menu['description'])) ?></p>

            <p>
                <strong>Minimum :</strong> <?= (int)$menu['personnes_min'] ?> personnes
                — <strong>Prix / personne :</strong> <?= number_format((float)$menu['prix_par_personne'], 2, ',', ' ') ?> €
            </p>

            <!-- Lien vers la page de détail du menu -->
            <a class="btn" href="index.php?page=menu&id=<?= (int)$menu['id'] ?>">Voir le détail</a>
        </article>
    <?php endforeach; ?>
<?php endif; ?>
