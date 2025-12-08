<?php
$pageTitle = 'Connexion - Vite & Gourmand';
require __DIR__ . '/../partials/header.php';
?>

<h2>Connexion</h2>

<form method="post" action="index.php?page=login_post">
    <label>Email :</label>
    <input type="email" name="email" required>

    <label>Mot de passe :</label>
    <input type="password" name="password" required>

    <br><br>
    <button type="submit">Se connecter</button>
</form>

<?php require __DIR__ . '/../partials/footer.php'; ?>
