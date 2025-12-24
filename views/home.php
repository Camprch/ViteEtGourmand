<?php
// $menus vient du HomeController
$pageTitle = 'Accueil - Vite & Gourmand';
require __DIR__ . '/partials/header.php';
?>

<section>
    <h2>Présentation de l'entreprise</h2>
    <p>
        Vite & Gourmand accompagne depuis plus de 25 ans ses clients pour tous types d’événements :
        repas familiaux, fêtes traditionnelles, anniversaires ou prestations professionnelles.
        Julie & José mettent en avant leur savoir-faire artisanal, leur réactivité et leur exigence
        de qualité pour proposer des menus raffinés, adaptés à tous les besoins.
    </p>

    <p>
        Notre équipe est reconnue pour son professionnalisme, son sens du service et son
        accompagnement personnalisé. Chaque prestation est préparée avec soin afin d'assurer
        une expérience gourmande, conviviale et sans stress.
    </p>
</section>

<section>
    <h2>Avis de nos clients</h2>

    <?php if (empty($avis)): ?>
        <p>Aucun avis pour le moment.</p>
    <?php else: ?>
        <ul>
            <?php foreach ($avis as $a): ?>
                <li>
                    <strong><?= htmlspecialchars($a['prenom']) ?></strong> —
                    Note : <?= (int)$a['note'] ?>/5<br>
                    <?= nl2br(htmlspecialchars($a['commentaire'])) ?><br>
                    <small><?= htmlspecialchars($a['date']) ?></small>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>
</section>

<h2>Nos menus</h2>

<?php if (empty($menus)): ?>
    <p>Aucun menu pour le moment.</p>
<?php else: ?>
    <ul>
        <?php foreach ($menus as $menu): ?>
            <li>
                <strong><?= htmlspecialchars($menu['titre']) ?></strong><br>
                <?= nl2br(htmlspecialchars($menu['description'])) ?><br>
                Min. <?= (int)$menu['personnes_min'] ?> personnes –
                <?= number_format((float)$menu['prix_par_personne'], 2, ',', ' ') ?> € / personne
            </li>
        <?php endforeach; ?>
    </ul>
<?php endif; ?>

<?php
require __DIR__ . '/partials/footer.php';
