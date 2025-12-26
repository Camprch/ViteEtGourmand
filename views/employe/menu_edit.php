<?php require __DIR__ . '/../partials/header.php'; ?>

<h1>Modifier un menu</h1>

<form method="post" action="index.php?page=employe_menu_update">
    <input type="hidden" name="_csrf" value="<?= htmlspecialchars(Csrf::token()) ?>">
    <input type="hidden" name="id" value="<?= (int)$menu['id'] ?>">

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

<p><a href="index.php?page=employe_menus">← Retour liste</a></p>

<?php require __DIR__ . '/../partials/footer.php'; ?>
