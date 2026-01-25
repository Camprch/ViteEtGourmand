<?php
declare(strict_types=1);

// Vue : liste partielle des menus

// Utilisé par : views/menu/index.php (affichage initial et AJAX)

/** @var array $menus */
?>

<!-- Affichage si aucun menu ne correspond aux filtres -->
<?php if (empty($menus)): ?>
    <p>Aucun menu ne correspond à vos filtres.</p>
<?php else: ?>
    <!-- Boucle sur chaque menu pour affichage sous forme de carte -->
    <div class="cards-grid">
        <?php foreach ($menus as $menu): ?>
            <article class="card menu-card">
                <div class="menu-card-media">
                    <?php if (!empty($menu['image_chemin'])): ?>
                        <img src="<?= htmlspecialchars((string)$menu['image_chemin']) ?>"
                             alt="<?= htmlspecialchars((string)($menu['image_alt'] ?? $menu['titre'] ?? 'Menu')) ?>">
                    <?php else: ?>
                        <div class="placeholder">Aucune image</div>
                    <?php endif; ?>
                </div>
                <h3><?= htmlspecialchars((string)$menu['titre']) ?></h3>
            
            <!-- Indication de rupture de stock -->
            <?php if (array_key_exists('stock', $menu) && $menu['stock'] !== null && (int)$menu['stock'] <= 0): ?>
                <p><span class="badge badge-warn">Indisponible</span></p>
            <?php endif; ?>

            <p><?= nl2br(htmlspecialchars((string)$menu['description'])) ?></p>

            <p class="muted">
                Minimum <?= (int)$menu['personnes_min'] ?> personnes
                • <?= number_format((float)$menu['prix_par_personne'], 2, ',', ' ') ?> € / personne
            </p>

            <!-- Lien vers la page de détail du menu -->
            <a class="btn" href="index.php?page=menu&id=<?= (int)$menu['id'] ?>">Voir le détail</a>
        </article>
        <?php endforeach; ?>
    </div>
<?php endif; ?>
