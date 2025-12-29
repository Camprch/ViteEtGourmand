<?php

// Vue : formulaire de contact pour les utilisateurs du site

$pageTitle = 'Contact - Vite & Gourmand';
require __DIR__ . '/../partials/header.php';
?>

<h2>Contact</h2>

<form method="post" action="index.php?page=contact_post">
    <?php require_once __DIR__ . '/../../src/security/Csrf.php'; ?>
    <input type="hidden" name="_csrf" value="<?= htmlspecialchars(Csrf::token()) ?>">
    
    <label for="nom">Nom</label>
    <input id="nom" name="nom" type="text" required>

    <label for="email">Email</label>
    <input id="email" name="email" type="email" required>

    <label for="titre">Titre</label>
    <input id="titre" name="titre" type="text" required>

    <label for="message">Message</label>
    <textarea id="message" name="message" rows="6" required></textarea>

    <button type="submit">Envoyer</button>
</form>

<?php require __DIR__ . '/../partials/footer.php'; ?>
