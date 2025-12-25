<?php
$pageTitle = "Réinitialiser le mot de passe";
require __DIR__ . '/../partials/header.php';

$token = htmlspecialchars(trim($_GET['token'] ?? ''), ENT_QUOTES);
?>

<h2>Réinitialiser le mot de passe</h2>

<form method="post" action="index.php?page=reset_password_post">
    <input type="hidden" name="_csrf" value="<?= htmlspecialchars(Csrf::token()) ?>">
    <input type="hidden" name="token" value="<?= $token ?>">

    <label for="password">Nouveau mot de passe</label>
    <input id="password" name="password" type="password" required>

    <label for="password_confirm">Confirmer le mot de passe</label>
    <input id="password_confirm" name="password_confirm" type="password" required>

    <button type="submit">Mettre à jour</button>
</form>

<?php require __DIR__ . '/../partials/footer.php'; ?>
