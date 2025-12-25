<?php
$pageTitle = "Mot de passe oublié";
require __DIR__ . '/../partials/header.php';
?>

<h2>Mot de passe oublié</h2>

<form method="post" action="index.php?page=forgot_password_post">
    <input type="hidden" name="_csrf" value="<?= htmlspecialchars(Csrf::token()) ?>">
    <label for="email">Email</label>
    <input id="email" name="email" type="email" required>

    <button type="submit">Envoyer le lien</button>
</form>

<p><a href="index.php?page=login">Retour connexion</a></p>

<?php require __DIR__ . '/../partials/footer.php'; ?>
