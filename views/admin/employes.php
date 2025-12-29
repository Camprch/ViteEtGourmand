<?php

// Vue : gestion des employés (création, activation/désactivation, liste)

$pageTitle = "Gestion employés - Admin";
require __DIR__ . '/../partials/header.php';
?>

<h2>Gestion des employés</h2>

<h3>Créer un employé</h3>
<form method="post" action="index.php?page=admin_employe_create">
    <input type="hidden" name="_csrf" value="<?= htmlspecialchars(Csrf::token()) ?>">
    <label>Email</label>
    <input type="email" name="email" required>

    <label>Mot de passe</label>
    <input type="password" name="password" required>

    <label>Nom (optionnel)</label>
    <input type="text" name="nom">

    <label>Prénom (optionnel)</label>
    <input type="text" name="prenom">

    <button type="submit">Créer</button>
</form>

<hr>

<h3>Employés existants</h3>

<?php if (empty($employes)): ?>
    <p>Aucun employé.</p>
<?php else: ?>
    <table border="1" cellpadding="6" cellspacing="0">
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
                <td><?= (int)$e['actif'] === 1 ? 'Oui' : 'Non' ?></td>
                <td>
                    <form method="post" action="index.php?page=admin_employe_toggle">
                        <input type="hidden" name="_csrf" value="<?= htmlspecialchars(Csrf::token()) ?>">
                        <input type="hidden" name="id" value="<?= (int)$e['id'] ?>">
                        <input type="hidden" name="actif" value="<?= (int)$e['actif'] === 1 ? 0 : 1 ?>">
                        <button type="submit">
                            <?= (int)$e['actif'] === 1 ? 'Désactiver' : 'Activer' ?>
                        </button>
                    </form>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>

<p><a href="index.php?page=dashboard_admin">Retour dashboard</a></p>

<?php require __DIR__ . '/../partials/footer.php'; ?>
