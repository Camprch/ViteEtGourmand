<?php

// Vue : formulaire de demande de réinitialisation de mot de passe

$pageTitle = "Mot de passe oublié";
require __DIR__ . '/../partials/header.php';
?>

<section class="form-shell">
    <div class="form-card">
        <p class="eyebrow">Accès sécurisé</p>
        <h2>Mot de passe oublié</h2>
        <p class="muted">Nous envoyons un lien de réinitialisation à votre email.</p>

        <form method="post" action="index.php?page=forgot_password_post" class="form-stack">
            <input type="hidden" name="_csrf" value="<?= htmlspecialchars(Csrf::token()) ?>">
            <label for="email">Email</label>
            <input id="email" name="email" type="email" required>

            <button type="submit">Envoyer le lien</button>
        </form>

        <p class="muted"><a href="index.php?page=login">Retour connexion</a></p>
    </div>
</section>

<?php require __DIR__ . '/../partials/footer.php'; ?>
