<?php

// Vue : Menu d'édition pour les employés

// Utilisé par : EmployeMenuController::edit()
require __DIR__ . '/../partials/header.php';
?>


<!-- Titre de la page -->
<h1>Modifier un menu</h1>


<!-- Formulaire d'édition du menu -->
<form method="post" action="index.php?page=employe_menu_update">
    <!-- Protection CSRF et identifiant du menu -->
    <input type="hidden" name="_csrf" value="<?= htmlspecialchars(Csrf::token()) ?>">
    <input type="hidden" name="id" value="<?= (int)$menu['id'] ?>">

    <!-- Champs principaux du menu -->
    <div>
        <label>Titre</label><br>
        <input name="titre" value="<?= htmlspecialchars($menu['titre']) ?>" required>
    </div>

    <div>
        <label>Description</label><br>
        <textarea name="description" required><?= htmlspecialchars($menu['description']) ?></textarea>
    </div>

    <div>
        <label>Thème</label><br>
        <input name="theme" value="<?= htmlspecialchars($menu['theme'] ?? '') ?>">
    </div>

    <div>
        <label>Régime</label><br>
        <input name="regime" value="<?= htmlspecialchars($menu['regime'] ?? '') ?>">
    </div>

    <div>
        <label>Prix par personne</label><br>
        <input type="number" step="0.01" name="prix_par_personne" value="<?= htmlspecialchars((string)$menu['prix_par_personne']) ?>" required>
    </div>

    <div>
        <label>Personnes min</label><br>
        <input type="number" name="personnes_min" value="<?= (int)$menu['personnes_min'] ?>" required>
    </div>

    <div>
        <label>Conditions particulières</label><br>
        <textarea name="conditions_particulieres"><?= htmlspecialchars($menu['conditions_particulieres'] ?? '') ?></textarea>
    </div>

    <div>
        <label>Stock (vide = illimité)</label><br>
        <input type="number" name="stock" min="0" value="<?= htmlspecialchars((string)($menu['stock'] ?? '')) ?>">
    </div>

    <hr>
    <!-- Section de gestion des plats associés au menu -->
    <h2>Plats du menu</h2>

    <p>Coche les plats inclus et donne un ordre (optionnel). Si l’ordre est vide, le plat sera mis après ceux ordonnés.</p>

    <table border="1" cellpadding="6">
        <thead>
            <tr>
                <th>Inclure</th>
                <th>Type</th>
                <th>Nom</th>
                <th>Ordre</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($plats as $p): ?>
            <?php
                $pid = (int)$p['id'];
                $checked = array_key_exists($pid, $menuPlatMap);
                $ordreVal = $checked ? $menuPlatMap[$pid] : null;
            ?>
            <tr>
                <td>
                    <input type="checkbox" name="plats[]" value="<?= $pid ?>" <?= $checked ? 'checked' : '' ?>>
                </td>
                <td><?= htmlspecialchars($p['type']) ?></td>
                <td><?= htmlspecialchars($p['nom']) ?></td>
                <td>
                    <input type="number"
                        name="plats_ordre[<?= $pid ?>]"
                        min="0"
                        value="<?= htmlspecialchars((string)($ordreVal ?? '')) ?>"
                        style="width:80px">
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>

    <button type="submit">Enregistrer</button>
</form>


<hr>
    <!-- Section de gestion des images du menu -->
    <h2>Images du menu</h2>

    <!-- Formulaire d'upload d'une nouvelle image -->
    <form method="post"
        action="index.php?page=employe_menu_image_upload"
        enctype="multipart/form-data">

        <input type="hidden" name="_csrf" value="<?= htmlspecialchars(Csrf::token()) ?>">
        <input type="hidden" name="menu_id" value="<?= (int)$menu['id'] ?>">

        <div>
            <input type="file" name="image" accept="image/*" required>
        </div>

        <div>
            <label>Texte alternatif</label><br>
            <input name="alt">
        </div>

        <label>
            <input type="checkbox" name="is_main">
            Image principale
        </label>

        <button type="submit">Uploader</button>
    </form>

    <!-- Affichage des images existantes du menu -->
    <?php if (!empty($images)): ?>
        <h3>Images existantes</h3>
        <?php foreach ($images as $img): ?>
            <div style="margin:10px 0;">
                <img src="uploads/menus/<?= htmlspecialchars($img['chemin']) ?>"
                    alt="<?= htmlspecialchars($img['alt_text'] ?? '') ?>"
                    style="max-width:200px; display:block;">

                <?php if ($img['is_principale']): ?>
                    <strong>Image principale</strong>
                <?php endif; ?>

                <!-- Formulaire pour supprimer une image -->
                <form method="post" action="index.php?page=employe_menu_image_delete">
                    <input type="hidden" name="_csrf" value="<?= htmlspecialchars(Csrf::token()) ?>">
                    <input type="hidden" name="id" value="<?= (int)$img['id'] ?>">
                    <input type="hidden" name="menu_id" value="<?= (int)$menu['id'] ?>">
                    <button type="submit">Supprimer</button>
                </form>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>


<!-- Lien de retour vers la liste des menus -->
<p><a href="index.php?page=employe_menus">← Retour liste</a></p>

<?php require __DIR__ . '/../partials/footer.php'; ?>
