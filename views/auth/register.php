<?php

// Vue : formulaire de création de compte utilisateur

$pageTitle = 'Créer un compte - Vite & Gourmand';
require __DIR__ . '/../partials/header.php';
?>

<h2>Créer un compte</h2>

<form method="post" action="index.php?page=register_post">
    <input type="hidden" name="_csrf" value="<?= htmlspecialchars(Csrf::token()) ?>">

    <label>Nom :</label>
    <input type="text" name="nom" required>

    <label>Prénom :</label>
    <input type="text" name="prenom" required>

    <label>Email :</label>
    <input type="email" name="email" required>

    <label>Téléphone :</label>
    <input type="text" name="telephone">

    <label>Adresse :</label>
    <input type="text" name="adresse">

    <label>Mot de passe :</label>
    <input type="password" name="password" required>

    <label>Confirmer le mot de passe :</label>
    <input type="password" name="password_confirm" required>

    <br><br>
    <button type="submit">Créer mon compte</button>
</form>

<?php require __DIR__ . '/../partials/footer.php'; ?>
