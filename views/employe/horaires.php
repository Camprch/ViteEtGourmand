<?php
$pageTitle = "Gestion des horaires";
require __DIR__ . '/../partials/header.php';

// index par jour pour accès facile
$byJour = [];
foreach ($horaires as $h) {
    $byJour[$h['jour']] = $h;
}
?>

<h2>Gestion des horaires</h2>

<?php if (!empty($_GET['ok'])): ?>
    <p><strong>Horaires mis à jour ✅</strong></p>
<?php endif; ?>

<form method="post" action="index.php?page=employe_horaires_update">
    <input type="hidden" name="_csrf" value="<?= htmlspecialchars(Csrf::token()) ?>">

    <table border="1" cellpadding="6" cellspacing="0">
        <thead>
            <tr>
                <th>Jour</th>
                <th>Ouverture</th>
                <th>Fermeture</th>
                <th>Fermé</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach (['Lundi','Mardi','Mercredi','Jeudi','Vendredi','Samedi','Dimanche'] as $jour): ?>
            <?php $h = $byJour[$jour] ?? ['heure_ouverture'=>null,'heure_fermeture'=>null,'ferme'=>0]; ?>
            <tr>
                <td><?= htmlspecialchars($jour) ?></td>
                <td>
                    <input type="time"
                        name="heure_ouverture[<?= htmlspecialchars($jour) ?>]"
                        value="<?= htmlspecialchars(!empty($h['heure_ouverture']) ? substr((string)$h['heure_ouverture'], 0, 5) : '') ?>">
                </td>
                <td>
                    <input type="time"
                        name="heure_fermeture[<?= htmlspecialchars($jour) ?>]"
                        value="<?= htmlspecialchars(!empty($h['heure_fermeture']) ? substr((string)$h['heure_fermeture'], 0, 5) : '') ?>">
                </td>
                <td style="text-align:center;">
                    <input type="checkbox"
                           name="ferme[<?= htmlspecialchars($jour) ?>]"
                           value="1"
                           <?= !empty($h['ferme']) ? 'checked' : '' ?>>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>

    <p>
        <button type="submit">Enregistrer</button>
        <a href="index.php?page=dashboard_employe">Retour dashboard</a>
    </p>
</form>

<?php require __DIR__ . '/../partials/footer.php'; ?>
