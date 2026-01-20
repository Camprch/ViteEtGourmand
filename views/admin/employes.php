<?php

// Vue : gestion des employés (création, activation/désactivation, liste)

$pageTitle = "Gestion employés - Admin";
require __DIR__ . '/../partials/header.php';
?>

<section class="page-head">
    <div>
        <p class="eyebrow">Administration</p>
        <h2>Gestion des employés</h2>
    </div>
</section>

<section class="card">
    <h3>Créer un employé</h3>
    <form method="post" action="index.php?page=admin_employe_create" class="form-grid">
        <input type="hidden" name="_csrf" value="<?= htmlspecialchars(Csrf::token()) ?>">
        <label>Email
            <input type="email" name="email" required>
        </label>

        <label>Mot de passe
            <input type="password" name="password" required>
        </label>

        <label>Nom (optionnel)
            <input type="text" name="nom">
        </label>

        <label>Prénom (optionnel)
            <input type="text" name="prenom">
        </label>

        <div class="form-actions span-2">
            <button type="submit">Créer</button>
        </div>
    </form>
</section>

<section>
<h3>Employés existants</h3>

<?php if (empty($employes)): ?>
    <p>Aucun employé.</p>
<?php else: ?>
    <div class="table-wrap">
    <table class="table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Nom</th>
                <th>Email</th>
                <th>Actif</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($employes as $e): ?>
            <tr>
                <td><?= (int)$e['id'] ?></td>
                <td><?= htmlspecialchars($e['prenom'] . ' ' . $e['nom']) ?></td>
                <td><?= htmlspecialchars($e['email']) ?></td>
                <td>
                    <?php if ((int)$e['actif'] === 1): ?>
                        <span class="status-pill badge-success">Actif</span>
                    <?php else: ?>
                        <span class="status-pill badge-danger">Inactif</span>
                    <?php endif; ?>
                </td>
                <td>
                    <form method="post" action="index.php?page=admin_employe_toggle">
                        <input type="hidden" name="_csrf" value="<?= htmlspecialchars(Csrf::token()) ?>">
                        <input type="hidden" name="id" value="<?= (int)$e['id'] ?>">
                        <input type="hidden" name="actif" value="<?= (int)$e['actif'] === 1 ? 0 : 1 ?>">
                        <button class="btn-sm" type="submit">
                            <?= (int)$e['actif'] === 1 ? 'Désactiver' : 'Activer' ?>
                        </button>
                    </form>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
    </div>
<?php endif; ?>
</section>

<section class="cta-bar">
    <a class="btn btn-ghost" href="index.php?page=dashboard_admin">Retour dashboard</a>
</section>

<?php require __DIR__ . '/../partials/footer.php'; ?>
