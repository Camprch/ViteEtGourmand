<?php

// $menu est fourni par le controller

$pageTitle = "Commander : " . htmlspecialchars($menu['titre']);
require __DIR__ . '/../partials/header.php';
?>

<!-- Vue : formulaire de création d'une commande pour un menu donné -->

<section class="order-shell">
    <div class="order-card">
        <p class="eyebrow">Commande</p>
        <h2>Commander : <?= htmlspecialchars($menu['titre']) ?></h2>
        <p class="muted">
            Minimum <?= (int)$menu['personnes_min'] ?> personnes •
            <?= number_format((float)$menu['prix_par_personne'], 2, ',', ' ') ?> € / personne
        </p>

        <?php require_once __DIR__ . '/../../src/security/Csrf.php'; ?>

        <form method="post" action="index.php?page=commande_traitement" class="form-grid">
            <input type="hidden" name="id_menu" value="<?= (int)$menu['id'] ?>">
            <input type="hidden" name="_csrf" value="<?= htmlspecialchars(Csrf::token()) ?>">

            <label>Nombre de personnes
                <input type="number" name="nb_personnes" min="<?= (int)$menu['personnes_min'] ?>" required>
            </label>

            <label>Date de prestation
                <input type="date" name="date_prestation" required>
            </label>

            <label>Heure de prestation
                <input type="time" name="heure_prestation" required>
            </label>

            <label class="span-2">Adresse de prestation
                <input type="text" name="adresse_prestation" required>
            </label>

            <label>Ville
                <input type="text" name="ville" required>
            </label>

            <label>Code postal
                <input type="text" name="code_postal" required>
            </label>

            <label>Distance depuis Bordeaux (en km)
                <input type="number" name="distance_km" min="0" step="0.1" value="0">
            </label>

            <div class="form-actions span-2">
                <button type="submit">Valider la commande</button>
                <a class="btn btn-ghost" href="index.php?page=menus">Retour aux menus</a>
            </div>
        </form>
    </div>
    <div class="order-aside">
        <h3>Comment ça se passe ?</h3>
        <ol class="steps">
            <li>Choisissez votre menu.</li>
            <li>Indiquez la date et l’adresse.</li>
            <li>Recevez la confirmation par email.</li>
        </ol>
    </div>
</section>

<?php require __DIR__ . '/../partials/footer.php'; ?>
