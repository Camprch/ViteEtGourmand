<?php

// Vue : statistiques des commandes (NoSQL/MongoDB + graphique)

// Permet de filtrer par date et d'afficher un graphique des ventes par menu.

$pageTitle = "Statistiques - Admin";
require __DIR__ . '/../partials/header.php';
?>

<section class="page-head">
    <div>
        <p class="eyebrow">Administration</p>
        <h2>Statistiques</h2>
        <p class="muted">Analyse des commandes acceptées (MongoDB).</p>
    </div>
</section>

<section class="card">
    <form method="get" action="index.php" class="form-inline">
        <input type="hidden" name="page" value="admin_stats">

        <label>Du
            <input type="date" name="from" value="<?= htmlspecialchars($_GET['from'] ?? '') ?>">
        </label>

        <label>Au
            <input type="date" name="to" value="<?= htmlspecialchars($_GET['to'] ?? '') ?>">
        </label>

        <button type="submit">Mettre à jour</button>
    </form>
</section>

<?php if (!empty($error)): ?>
    <section class="card">
        <h3>MongoDB non disponible</h3>
        <p><?= htmlspecialchars($error) ?></p>

    <p><strong>À faire pour valider l’exigence NoSQL :</strong></p>
    <ul>
        <li>Ajouter <code>MONGO_DSN</code> et <code>MONGO_DB</code> dans <code>.env</code></li>
        <li>Installer/activer l’extension PHP MongoDB (ou configurer l’environnement de déploiement)</li>
    </ul>

        <p><a href="index.php?page=dashboard_admin">Retour dashboard</a></p>
    </section>
    <?php require __DIR__ . '/../partials/footer.php'; exit; ?>
<?php endif; ?>

<?php if (empty($stats)): ?>
    <section class="card">
        <p>Aucune donnée pour la période sélectionnée.</p>
        <p class="muted"><small>Note : seules les commandes passées au statut <strong>ACCEPTEE</strong> sont enregistrées dans MongoDB.</small></p>
    </section>
<?php else: ?>
    <section class="cards-grid">
        <div class="card">
            <h3>Résumé</h3>
            <p><strong>Nb commandes acceptées :</strong> <?= (int)$nbTotal ?></p>
            <p><strong>Chiffre d’affaires total :</strong> <?= number_format((float)$caTotal, 2, ',', ' ') ?> €</p>
        </div>
        <div class="card">
            <h3>Commandes par menu</h3>
            <p class="muted">Comparatif par menu pour la période sélectionnée.</p>
        </div>
    </section>

    <section class="card">
    <div class="table-wrap">
    <table class="table">
        <thead>
        <tr>
            <th>Menu</th>
            <th>Nb commandes</th>
            <th>CA (€)</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($stats as $s): ?>
            <tr>
                <td><?= htmlspecialchars($s['menu_titre']) ?></td>
                <td><?= (int)$s['nb_commandes'] ?></td>
                <td><?= number_format((float)$s['chiffre_affaires'], 2, ',', ' ') ?></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
    </div>
    </section>

    <section class="chart-card">
        <h3>Graphique</h3>
        <canvas id="chart" width="900" height="380"></canvas>
    </section>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        const data = <?= json_encode(
            $stats,
            JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT
        ) ?>;

        const labels = data.map(x => x.menu_titre);
        const nb = data.map(x => x.nb_commandes);
        const ca = data.map(x => x.chiffre_affaires);

        new Chart(document.getElementById('chart'), {
            type: 'bar',
            data: {
                labels,
                datasets: [
                    { label: 'Nb commandes', data: nb },
                    { label: 'Chiffre d’affaires (€)', data: ca },
                ]
            }
        });
    </script>
<?php endif; ?>

<?php require __DIR__ . '/../partials/footer.php'; ?>
