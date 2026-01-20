<?php

// Vue : formulaire de connexion utilisateur

$pageTitle = 'Connexion - Vite & Gourmand';
require __DIR__ . '/../partials/header.php';
?>

<section class="form-shell">
    <div class="form-card">
        <p class="eyebrow">Espace client</p>
        <h2>Connexion</h2>

        <form method="post" action="index.php?page=login_post" class="form-stack">
            <input type="hidden" name="_csrf" value="<?= htmlspecialchars(Csrf::token()) ?>">
            <label for="email">Email</label>
            <input id="email" type="email" name="email" required>

            <label for="password">Mot de passe</label>
            <input id="password" type="password" name="password" required>

            <button type="submit">Se connecter</button>
        </form>

        <p class="muted">
            <a href="index.php?page=forgot_password">Mot de passe oublié ?</a>
        </p>

        <p class="muted">
            Pas encore de compte ?
            <a href="index.php?page=register">Créer un compte</a>
        </p>
    </div>
    <div class="form-aside">
        <h3>Bienvenue chez Vite & Gourmand</h3>
        <p>
            Accédez à vos commandes, suivez vos prestations et gérez vos informations
            en quelques clics.
        </p>
    </div>
</section>

<?php if (isset($_GET['password_changed'])): ?>
    <section class="notice">✅ Mot de passe modifié. Merci de vous reconnecter.</section>
<?php endif; ?>

<?php require __DIR__ . '/../partials/footer.php'; ?>
