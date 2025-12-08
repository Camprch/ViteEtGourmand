<?php
// $menu vient du contrôleur
$pageTitle = 'Menu - ' . htmlspecialchars($menu['titre']);
require __DIR__ . '/../partials/header.php';
?>

<h2><?= htmlspecialchars($menu['titre']) ?></h2>

<p><?= nl2br(htmlspecialchars($menu['description'])) ?></p>

<ul>
    <li>Thème : <?= htmlspecialchars($menu['theme']) ?></li>
    <li>Régime : <?= htmlspecialchars($menu['regime']) ?></li>
    <li>Minimum : <?= (int)$menu['personnes_min'] ?> personnes</li>
    <li>Prix par personne :
        <?= number_format((float)$menu['prix_par_personne'], 2, ',', ' ') ?> €</li>
    <li>Stock disponible : <?= (int)$menu['stock'] ?></li>
</ul>

<?php if (!empty($menu['conditions_particulieres'])): ?>
    <h3>Conditions particulières</h3>
    <p><?= nl2br(htmlspecialchars($menu['conditions_particulieres'])) ?></p>
<?php endif; ?>

<p>
    <a href="index.php?page=menus">← Retour aux menus</a>
</p>

<p>
    <a href="index.php?page=commande&menu_id=<?= (int)$menu['id'] ?>">
        Commander ce menu
    </a>
</p>

<?php require __DIR__ . '/../partials/footer.php'; ?>
