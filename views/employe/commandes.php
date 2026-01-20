<?php

// Vue : Commandes pour les employés

// Utilisé par : EmployeCommandeController::index()

$pageTitle = "Commandes - Employé";
require __DIR__ . '/../partials/header.php';

// Liste des statuts possibles pour filtrer et modifier les commandes
$statuts = [
    '' => 'Tous',
    'EN_ATTENTE' => 'En attente',
    'ACCEPTEE' => 'Acceptée',
    'EN_PREPARATION' => 'En préparation',
    'EN_LIVRAISON' => 'En livraison',
    'LIVREE' => 'Livrée',
    'ATTENTE_RETOUR_MATERIEL' => 'Attente retour matériel',
    'TERMINEE' => 'Terminée',
    'ANNULEE' => 'Annulée',
];
$current = $_GET['statut'] ?? '';
?>

<section class="page-head">
    <div>
        <p class="eyebrow">Commandes</p>
        <h2>Gestion des commandes</h2>
        <p class="muted">Filtrez, mettez à jour les statuts ou annulez une commande.</p>
    </div>
</section>

<!-- Formulaire de filtrage des commandes par statut -->
<section class="card">
    <form method="get" action="index.php" class="form-inline">
        <input type="hidden" name="page" value="employe_commandes">
        <label for="statut">Filtrer par statut</label>
        <select id="statut" name="statut">
            <?php foreach ($statuts as $value => $label): ?>
                <option value="<?= htmlspecialchars($value) ?>" <?= ($current === $value) ? 'selected' : '' ?>>
                    <?= htmlspecialchars($label) ?>
                </option>
            <?php endforeach; ?>
        </select>
        <button type="submit">Filtrer</button>
    </form>
</section>

<!-- Affichage de la liste des commandes ou message si aucune -->
<?php if (empty($commandes)): ?>
    <p>Aucune commande.</p>
<?php else: ?>
    <section>
    <div class="table-wrap">
    <table class="table">
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
                    <!-- Formulaire pour changer le statut de la commande -->
                    <form method="post" action="index.php?page=employe_commande_update_statut" class="action-row">
                        <input type="hidden" name="_csrf" value="<?= htmlspecialchars(Csrf::token()) ?>">
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
                        <button class="btn-sm" type="submit">OK</button>
                    </form>

                    <!-- Formulaire pour annuler la commande (nécessite motif et contact) -->
                    <form method="post" action="index.php?page=employe_commande_annuler" class="action-row">
                        <input type="hidden" name="_csrf" value="<?= htmlspecialchars(Csrf::token()) ?>">
                        <input type="hidden" name="id" value="<?= (int)$c['id'] ?>">
                        <input type="text" name="mode_contact" placeholder="Téléphone / Email" required>
                        <input type="text" name="motif" placeholder="Motif annulation" required>
                        <button class="btn btn-ghost btn-sm" type="submit">Annuler</button>
                    </form>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
    </div>
    </section>
<?php endif; ?>

<?php

// Détermination du dashboard de retour selon le rôle utilisateur
$user = $_SESSION['user'] ?? null;
$dashboard = $_SESSION['dashboard_context'] ?? (
    ($user && $user['role'] === 'ADMIN') ? 'dashboard_admin' : 'dashboard_employe'
);
?>

<!-- Lien de retour vers le dashboard adapté -->
<section class="cta-bar">
    <a class="btn btn-ghost" href="index.php?page=<?= $dashboard ?>">Retour dashboard</a>
</section>

<?php require __DIR__ . '/../partials/footer.php'; ?>
