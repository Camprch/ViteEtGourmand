<?php

// Vue : formulaire de création de compte utilisateur

$pageTitle = 'Créer un compte - Vite & Gourmand';
require __DIR__ . '/../partials/header.php';
?>

<section class="form-shell">
    <div class="form-card">
        <p class="eyebrow">Première visite</p>
        <h2>Créer un compte</h2>

        <form method="post" action="index.php?page=register_post" class="form-grid">
            <input type="hidden" name="_csrf" value="<?= htmlspecialchars(Csrf::token()) ?>">

            <label>Nom
                <input type="text" name="nom" required>
            </label>

            <label>Prénom
                <input type="text" name="prenom" required>
            </label>

            <label>Email
                <input type="email" name="email" required>
            </label>

            <label>Téléphone
                <input type="text" name="telephone">
            </label>

            <label class="span-2">Adresse
                <input type="text" name="adresse">
            </label>

            <label>Mot de passe
                <input type="password" name="password" required>
            </label>

            <label>Confirmer le mot de passe
                <input type="password" name="password_confirm" required>
            </label>

            <div class="form-actions span-2">
                <button type="submit">Créer mon compte</button>
                <a class="btn btn-ghost" href="index.php?page=login">Déjà un compte ?</a>
            </div>
        </form>
    </div>
    <div class="form-aside">
        <h3>Un suivi simple</h3>
        <p>Retrouvez vos commandes, téléchargez vos factures et recevez vos confirmations.</p>
    </div>
</section>

<?php require __DIR__ . '/../partials/footer.php'; ?>
