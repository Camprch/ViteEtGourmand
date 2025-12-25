<?php
$pageTitle = "Statistiques - Admin";
require __DIR__ . '/../partials/header.php';
?>

<h2>Statistiques (NoSQL + graphique)</h2>

<form method="get" action="index.php">
    <input type="hidden" name="page" value="admin_stats">

    <label>Du :
        <input type="date" name="from" value="<?= htmlspecialchars($_GET['from'] ?? '') ?>">
    </label>

    <label>Au :
        <input type="date" name="to" value="<?= htmlspecialchars($_GET['to'] ?? '') ?>">
    </label>

    <button type="submit">Mettre à jour</button>
</form>

<hr>

<?php if (!empty($error)): ?>
    <h3>MongoDB non disponible</h3>
    <p><?= htmlspecialchars($error) ?></p>

    <p><strong>À faire pour valider l’exigence NoSQL :</strong></p>
    <ul>
        <li>Ajouter <code>MONGO_DSN</code> et <code>MONGO_DB</code> dans <code>.env</code></li>
        <li>Installer/activer l’extension PHP MongoDB (ou configurer l’environnement de déploiement)</li>
    </ul>

    <p><a href="index.php?page=dashboard_admin">Retour admin</a></p>
    <?php require __DIR__ . '/../partials/footer.php'; exit; ?>
<?php endif; ?>

<?php if (empty($stats)): ?>
    <p>Aucune donnée.</p>
<?php else: ?>
    <h3>Commandes par menu</h3>

    <table border="1" cellpadding="6" cellspacing="0">
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

    <hr>

    <h3>Graphique</h3>
    <canvas id="chart" width="900" height="380"></canvas>

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

<p><a href="index.php?page=dashboard_admin">Retour dashboard</a></p>

<?php require __DIR__ . '/../partials/footer.php'; ?>
