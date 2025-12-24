<?php
$pageTitle = "Commandes - Employé";
require __DIR__ . '/../partials/header.php';

$statuts = [
    '' => 'Tous',
    'EN_ATTENTE' => 'En attente',
    'ACCEPTEE' => 'Acceptée',
    'EN_PREPARATION' => 'En préparation',
    'EN_LIVRAISON' => 'En livraison',
    'LIVREE' => 'Livrée',
    'ATTENTE_RETOUR_MATERIEL' => 'Attente retour matériel',
    'TERMINEE' => 'Terminée',
];
$current = $_GET['statut'] ?? '';
?>

<h2>Gestion des commandes</h2>

<form method="get" action="index.php">
    <input type="hidden" name="page" value="employe_commandes">
    <label for="statut">Filtrer par statut :</label>
    <select id="statut" name="statut">
        <?php foreach ($statuts as $value => $label): ?>
            <option value="<?= htmlspecialchars($value) ?>" <?= ($current === $value) ? 'selected' : '' ?>>
                <?= htmlspecialchars($label) ?>
            </option>
        <?php endforeach; ?>
    </select>
    <button type="submit">Filtrer</button>
</form>

<hr>

<?php if (empty($commandes)): ?>
    <p>Aucune commande.</p>
<?php else: ?>
    <table border="1" cellpadding="6" cellspacing="0">
        <thead>
            <tr>
                <th>ID</th>
                <th>Client</th>
                <th>Menu</th>
                <th>Date prestation</th>
                <th>Ville</th>
                <th>Total</th>
                <th>Statut</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($commandes as $c): ?>
            <tr>
                <td><?= (int)$c['id'] ?></td>
                <td><?= htmlspecialchars($c['prenom'] . ' ' . $c['nom']) ?></td>
                <td><?= htmlspecialchars($c['menu_titre']) ?></td>
                <td><?= htmlspecialchars($c['date_prestation']) ?> <?= htmlspecialchars(substr((string)$c['heure_prestation'], 0, 5)) ?></td>
                <td><?= htmlspecialchars($c['ville']) ?></td>
                <td><?= number_format((float)$c['prix_total'], 2, ',', ' ') ?> €</td>
                <td>
                    <form method="post" action="index.php?page=employe_commande_update_statut" style="margin-bottom:5px;">
                        <input type="hidden" name="id" value="<?= (int)$c['id'] ?>">
                        <select name="statut">
                            <?php foreach ($statuts as $value => $label): ?>
                                <?php if ($value !== ''): ?>
                                    <option value="<?= htmlspecialchars($value) ?>"
                                        <?= $c['statut_courant'] === $value ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($label) ?>
                                    </option>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </select>
                        <button type="submit">OK</button>
                    </form>

                    <form method="post" action="index.php?page=employe_commande_annuler">
                        <input type="hidden" name="id" value="<?= (int)$c['id'] ?>">
                        <input type="text" name="mode_contact" placeholder="Téléphone / Email" required>
                        <input type="text" name="motif" placeholder="Motif annulation" required>
                        <button type="submit">Annuler</button>
                    </form>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>

<?php
$user = $_SESSION['user'] ?? null;
$dashboard = ($user && $user['role'] === 'ADMIN')
    ? 'dashboard_admin'
    : 'dashboard_employe';
?>

<p><a href="index.php?page=<?= $dashboard ?>">Retour dashboard</a></p>

<?php require __DIR__ . '/../partials/footer.php'; ?>
