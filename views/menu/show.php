<?php

// Vue : détail d'un menu

// Utilisé par : route page=menu&id=...

// $menu, $plats, $image sont fournis par le contrôleur
$pageTitle = 'Menu - ' . htmlspecialchars($menu['titre']);
require __DIR__ . '/../partials/header.php';
?>


<section class="menu-hero">
    <div class="menu-hero-media">
        <?php if (!empty($image['chemin'])): ?>
            <img src="/uploads/menus/<?= htmlspecialchars($image['chemin']) ?>"
                 alt="<?= htmlspecialchars($image['alt_text'] ?? $menu['titre']) ?>">
        <?php else: ?>
            <div class="placeholder">Menu du moment</div>
        <?php endif; ?>
    </div>
    <div class="menu-hero-text">
        <p class="eyebrow">Menu signature</p>
        <h2><?= htmlspecialchars($menu['titre']) ?></h2>
        <p><?= nl2br(htmlspecialchars($menu['description'])) ?></p>
        <div class="menu-meta">
            <span class="badge"><?= htmlspecialchars((string)($menu['theme'] ?? '')) ?></span>
            <span class="badge badge-outline"><?= htmlspecialchars((string)($menu['regime'] ?? '')) ?></span>
            <span class="badge badge-outline">Min <?= (int)$menu['personnes_min'] ?> pers.</span>
            <span class="badge badge-outline">
                <?= number_format((float)$menu['prix_par_personne'], 2, ',', ' ') ?> € / pers.
            </span>
        </div>
    </div>
</section>


<!-- Affichage des conditions particulières si présentes -->
<?php if (!empty($menu['conditions_particulieres'])): ?>
    <section>
        <h3>Conditions particulières</h3>
        <p><?= nl2br(htmlspecialchars($menu['conditions_particulieres'])) ?></p>
    </section>
<?php endif; ?>


<?php
$isOutOfStock = ($menu['stock'] !== null && (int)$menu['stock'] <= 0);
?>
<!-- Affichage du bouton de commande ou message d'indisponibilité -->
<section class="cta-bar">
    <p class="muted">
        Stock :
        <?php if ($menu['stock'] === null): ?>
            Illimité
        <?php else: ?>
            <?= (int)$menu['stock'] ?>
        <?php endif; ?>
    </p>
    <div class="cta-actions">
        <a class="btn btn-ghost" href="index.php?page=menus">← Retour aux menus</a>
        <?php if ($isOutOfStock): ?>
            <span class="badge badge-warn">Indisponible</span>
        <?php else: ?>
            <a class="btn" href="index.php?page=commande&menu_id=<?= (int)$menu['id'] ?>">
                Commander ce menu
            </a>
        <?php endif; ?>
    </div>
</section>


<!-- Liste des plats inclus dans le menu -->
<section>
<h2>Plats inclus</h2>


<?php if (empty($plats)): ?>
    <p>Aucun plat associé pour le moment.</p>
<?php else: ?>
    <div class="cards-grid">
        <?php foreach ($plats as $p): ?>
            <article class="card">
                <p class="card-title">
                    <?= htmlspecialchars($p['type']) ?> —
                    <?= htmlspecialchars($p['nom']) ?>
                </p>

                <!-- Description du plat si présente -->
                <?php if (!empty($p['description'])): ?>
                    <p><?= htmlspecialchars($p['description']) ?></p>
                <?php endif; ?>

                <!-- Affichage des allergènes du plat si présents -->
                <?php if (!empty($p['allergenes'])): ?>
                    <p class="muted">
                        Allergènes :
                        <?php
                        $names = [];
                        foreach ($p['allergenes'] as $a) {
                            $names[] = $a['nom'];
                        }
                        echo htmlspecialchars(implode(', ', $names));
                        ?>
                    </p>
                <?php endif; ?>
            </article>
        <?php endforeach; ?>
    </div>
<?php endif; ?>
</section>


<?php require __DIR__ . '/../partials/footer.php'; ?>
