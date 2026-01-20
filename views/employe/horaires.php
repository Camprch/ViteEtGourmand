<?php

// Vue : horaires

// Utilisé par : EmployeHoraireController::index()
$pageTitle = "Gestion des horaires";
require __DIR__ . '/../partials/header.php';

// Indexation des horaires par jour pour accès facile dans le formulaire
$byJour = [];
foreach ($horaires as $h) {
    $byJour[$h['jour']] = $h;
}
?>

<section class="page-head">
    <div>
        <p class="eyebrow">Horaires</p>
        <h2>Gestion des horaires</h2>
    </div>
</section>

<!-- Message de confirmation après mise à jour -->
<?php if (!empty($_GET['ok'])): ?>
    <section class="notice"><strong>Horaires mis à jour ✅</strong></section>
<?php endif; ?>

<!-- Formulaire de modification des horaires d'ouverture par jour -->
<section class="card">
<form method="post" action="index.php?page=employe_horaires_update">
    <!-- Protection CSRF -->
    <input type="hidden" name="_csrf" value="<?= htmlspecialchars(Csrf::token()) ?>">

    <div class="table-wrap">
    <table class="table">
        <thead>
            <tr>
                <th>Jour</th>
                <th>Ouverture</th>
                <th>Fermeture</th>
                <th>Fermé</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach (["Lundi","Mardi","Mercredi","Jeudi","Vendredi","Samedi","Dimanche"] as $jour): ?>
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
    </div>

    <div class="form-actions">
        <button type="submit">Enregistrer</button>
        <a class="btn btn-ghost" href="index.php?page=dashboard_employe">Retour dashboard</a>
    </div>
</form>
</section>

<?php require __DIR__ . '/../partials/footer.php'; ?>
