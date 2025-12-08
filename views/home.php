<?php
// $menus vient du HomeController
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Vite & Gourmand - Accueil</title>
</head>
<body>
    <h1>Vite & Gourmand</h1>
    <p>Environnement OK. PHP tourne, PDO est configuré, on est prêts à développer.</p>

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
</body>
</html>
