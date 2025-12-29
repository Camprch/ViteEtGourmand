
<?php
// Fichier : menu/show.php
// Rôle : Affiche le détail d'un menu public, ses plats et ses informations
// Utilisé par : route page=menu&id=...
// $menu, $plats, $image sont fournis par le contrôleur
$pageTitle = 'Menu - ' . htmlspecialchars($menu['titre']);
require __DIR__ . '/../partials/header.php';
?>


<!-- Titre du menu -->
<h2><?= htmlspecialchars($menu['titre']) ?></h2>


<!-- Affichage de l'image principale du menu si disponible -->
<?php if ($image): ?>
    <img src="uploads/menus/<?= htmlspecialchars($image['chemin']) ?>"
         alt="<?= htmlspecialchars($image['alt_text'] ?? $menu['titre']) ?>"
         style="max-width:400px;">
<?php endif; ?>


<!-- Description du menu -->
<p><?= nl2br(htmlspecialchars($menu['description'])) ?></p>


<!-- Informations principales du menu -->
<ul>
    <li>Thème : <?= htmlspecialchars((string)($menu['theme'] ?? '')) ?></li>
    <li>Régime : <?= htmlspecialchars((string)($menu['regime'] ?? '')) ?></li>
    <li>Minimum : <?= (int)$menu['personnes_min'] ?> personnes</li>
    <li>Prix par personne :
        <?= number_format((float)$menu['prix_par_personne'], 2, ',', ' ') ?> €</li>
    <li>Stock disponible :
        <?php if ($menu['stock'] === null): ?>
            Illimité
        <?php else: ?>
            <?= (int)$menu['stock'] ?>
        <?php endif; ?>
    </li>
</ul>


<!-- Affichage des conditions particulières si présentes -->
<?php if (!empty($menu['conditions_particulieres'])): ?>
    <h3>Conditions particulières</h3>
    <p><?= nl2br(htmlspecialchars($menu['conditions_particulieres'])) ?></p>
<?php endif; ?>


<!-- Lien de retour vers la liste des menus -->
<p>
    <a href="index.php?page=menus">← Retour aux menus</a>
</p>


<?php
$isOutOfStock = ($menu['stock'] !== null && (int)$menu['stock'] <= 0);
?>
<!-- Affichage du bouton de commande ou message d'indisponibilité -->
<p>
    <?php if ($isOutOfStock): ?>
        <strong>Menu indisponible (rupture de stock).</strong>
    <?php else: ?>
        <a href="index.php?page=commande&menu_id=<?= (int)$menu['id'] ?>">
            Commander ce menu
        </a>
    <?php endif; ?>
</p>


<!-- Liste des plats inclus dans le menu -->
<h2>Plats inclus</h2>


<?php if (empty($plats)): ?>
    <p>Aucun plat associé pour le moment.</p>
<?php else: ?>
    <ul>
        <?php foreach ($plats as $p): ?>
            <li>
                <strong><?= htmlspecialchars($p['type']) ?> :</strong>
                <?= htmlspecialchars($p['nom']) ?>

                <!-- Description du plat si présente -->
                <?php if (!empty($p['description'])): ?>
                    — <?= htmlspecialchars($p['description']) ?>
                <?php endif; ?>

                <!-- Affichage des allergènes du plat si présents -->
                <?php if (!empty($p['allergenes'])): ?>
                    <div>
                        <small>
                            Allergènes :
                            <?php
                            $names = [];
                            foreach ($p['allergenes'] as $a) {
                                $names[] = $a['nom'];
                            }
                            echo htmlspecialchars(implode(', ', $names));
                            ?>
                        </small>
                    </div>
                <?php endif; ?>
            </li>
        <?php endforeach; ?>
    </ul>
<?php endif; ?>


<?php require __DIR__ . '/../partials/footer.php'; ?>
