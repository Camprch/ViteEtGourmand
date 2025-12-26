<?php
$pageTitle = 'Connexion - Vite & Gourmand';
require __DIR__ . '/../partials/header.php';
?>

<h2>Connexion</h2>

<form method="post" action="index.php?page=login_post">
    <input type="hidden" name="_csrf" value="<?= htmlspecialchars(Csrf::token()) ?>">
    <label for="email">Email :</label>
    <input id="email" type="email" name="email" required>

    <label for="password">Mot de passe :</label>
    <input id="password" type="password" name="password" required>

    <br><br>
    <button type="submit">Se connecter</button>
</form>

<p>
    <a href="index.php?page=forgot_password">Mot de passe oublié ?</a>
</p>

<?php if (isset($_GET['password_changed'])): ?>
    <p>✅ Mot de passe modifié. Merci de vous reconnecter.</p>
<?php endif; ?>

<?php require __DIR__ . '/../partials/footer.php'; ?>
