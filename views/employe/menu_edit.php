<?php

// Vue : Menu d'édition pour les employés

// Utilisé par : EmployeMenuController::edit()
require __DIR__ . '/../partials/header.php';
?>


<section class="page-head">
    <div>
        <p class="eyebrow">Menus</p>
        <h1>Modifier un menu</h1>
    </div>
</section>

<section class="card">
<form method="post" action="index.php?page=employe_menu_update" class="form-grid">
    <!-- Protection CSRF et identifiant du menu -->
    <input type="hidden" name="_csrf" value="<?= htmlspecialchars(Csrf::token()) ?>">
    <input type="hidden" name="id" value="<?= (int)$menu['id'] ?>">

    <!-- Champs principaux du menu -->
    <label>Titre
        <input name="titre" value="<?= htmlspecialchars($menu['titre']) ?>" required>
    </label>

    <label class="span-2">Description
        <textarea name="description" required><?= htmlspecialchars($menu['description']) ?></textarea>
    </label>

    <label>Thème
        <input name="theme" value="<?= htmlspecialchars($menu['theme'] ?? '') ?>">
    </label>

    <label>Régime
        <input name="regime" value="<?= htmlspecialchars($menu['regime'] ?? '') ?>">
    </label>

    <label>Prix par personne
        <input type="number" step="0.01" name="prix_par_personne" value="<?= htmlspecialchars((string)$menu['prix_par_personne']) ?>" required>
    </label>

    <label>Personnes min
        <input type="number" name="personnes_min" value="<?= (int)$menu['personnes_min'] ?>" required>
    </label>

    <label class="span-2">Conditions particulières
        <textarea name="conditions_particulieres"><?= htmlspecialchars($menu['conditions_particulieres'] ?? '') ?></textarea>
    </label>

    <label>Stock (vide = illimité)
        <input type="number" name="stock" min="0" value="<?= htmlspecialchars((string)($menu['stock'] ?? '')) ?>">
    </label>

    <hr class="span-2">
    <!-- Section de gestion des plats associés au menu -->
    <h2 class="span-2">Plats du menu</h2>

    <p class="span-2">Coche les plats inclus et donne un ordre (optionnel). Si l’ordre est vide, le plat sera mis après ceux ordonnés.</p>

    <div class="table-wrap span-2">
    <table class="table">
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
    </div>

        <div class="form-actions span-2">
            <button class="btn-sm" type="submit">Enregistrer</button>
        </div>
</form>
</section>

<section class="card">
    <!-- Section de gestion des images du menu -->
    <h2>Images du menu</h2>

    <!-- Formulaire d'upload d'une nouvelle image -->
    <form method="post"
        action="index.php?page=employe_menu_image_upload"
        enctype="multipart/form-data" class="form-grid">

        <input type="hidden" name="_csrf" value="<?= htmlspecialchars(Csrf::token()) ?>">
        <input type="hidden" name="menu_id" value="<?= (int)$menu['id'] ?>">

        <label class="span-2">
            <input type="file" name="image" accept="image/*" required>
        </label>

        <label>Texte alternatif
            <input name="alt">
        </label>

        <label>
            <input type="checkbox" name="is_main">
            Image principale
        </label>

        <div class="form-actions span-2">
            <button type="submit">Uploader</button>
        </div>
    </form>

    <!-- Affichage des images existantes du menu -->
    <?php if (!empty($images)): ?>
        <h3>Images existantes</h3>
        <div class="cards-grid">
            <?php foreach ($images as $img): ?>
                <article class="card">
                    <img src="uploads/menus/<?= htmlspecialchars($img['chemin']) ?>"
                        alt="<?= htmlspecialchars($img['alt_text'] ?? '') ?>"
                        style="max-width:100%; display:block; border-radius:12px;">

                    <?php if ($img['is_principale']): ?>
                        <p><span class="badge">Image principale</span></p>
                    <?php endif; ?>

                    <form method="post" action="index.php?page=employe_menu_image_delete" class="action-row">
                        <input type="hidden" name="_csrf" value="<?= htmlspecialchars(Csrf::token()) ?>">
                        <input type="hidden" name="id" value="<?= (int)$img['id'] ?>">
                        <input type="hidden" name="menu_id" value="<?= (int)$menu['id'] ?>">
                        <button class="btn btn-ghost btn-sm" type="submit">Supprimer</button>
                    </form>
                </article>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</section>


<!-- Lien de retour vers la liste des menus -->
<section class="cta-bar">
    <a class="btn btn-ghost" href="index.php?page=employe_menus">← Retour liste</a>
</section>

<?php require __DIR__ . '/../partials/footer.php'; ?>
