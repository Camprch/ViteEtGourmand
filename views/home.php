<?php

// Vue : affichage de la page d'accueil

// Utilisé par : route page=home

// $menus, $avis sont fournis par HomeController
$pageTitle = 'Accueil - Vite & Gourmand';
$pageDescription = 'Traiteur local pour repas de famille, anniversaires et événements professionnels.';
require __DIR__ . '/partials/header.php';
?>

<section class="hero">
    <div class="hero-text">
        <p class="eyebrow">Traiteur • Sur mesure • Local</p>
        <h2>Des menus chaleureux pour vos moments précieux</h2>
        <p>
            Vite & Gourmand accompagne depuis plus de 25 ans vos repas de famille, anniversaires
            et événements professionnels. Julie & José cultivent un savoir-faire artisanal,
            exigeant et généreux, pour une expérience sans stress.
        </p>
        <div class="hero-cta">
            <a class="btn" href="index.php?page=menus">Découvrir nos menus</a>
            <a class="btn btn-ghost" href="index.php?page=contact">Nous contacter</a>
        </div>
    </div>
    <div class="hero-card">
        <h3>Ce qui fait notre différence</h3>
        <ul class="list-check">
            <li>Produits frais et de saison</li>
            <li>Recettes traditionnelles revisitées</li>
            <li>Organisation fluide de la commande</li>
        </ul>
    </div>
</section>

<!-- Avis clients -->
<section>
    <h2>Avis de nos clients</h2>

    <?php if (empty($avis)): ?>
        <p>Aucun avis pour le moment.</p>
    <?php else: ?>
        <div class="cards-grid">
            <?php foreach ($avis as $a): ?>
                <article class="card">
                    <p class="card-title">
                        <?= htmlspecialchars((string)($a['prenom'] ?? '')) ?>
                        <span class="badge">Note <?= (int)$a['note'] ?>/5</span>
                    </p>
                    <p><?= nl2br(htmlspecialchars($a['commentaire'])) ?></p>
                    <small class="muted"><?= fr_datetime($a['date'] ?? null) ?></small>
                </article>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</section>

<!-- Menus mis en avant sur la page d'accueil -->
<section>
    <div class="section-head">
        <h2>Nos menus</h2>
        <a class="btn btn-ghost" href="index.php?page=menus">Voir tous les menus</a>
    </div>

    <?php if (empty($menus)): ?>
        <p>Aucun menu pour le moment.</p>
    <?php else: ?>
        <div class="cards-grid">
            <?php foreach ($menus as $menu): ?>
                <article class="card menu-card">
                    <h3><?= htmlspecialchars((string)($menu['titre'] ?? '')) ?></h3>
                    <p><?= nl2br(htmlspecialchars((string)($menu['description'] ?? ''))) ?></p>
                    <p class="muted">
                        Min. <?= (int)$menu['personnes_min'] ?> personnes •
                        <?= number_format((float)$menu['prix_par_personne'], 2, ',', ' ') ?> € / personne
                    </p>
                    <a class="btn" href="index.php?page=menu&id=<?= (int)$menu['id'] ?>">Voir le détail</a>
                </article>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</section>

<?php
require __DIR__ . '/partials/footer.php';
