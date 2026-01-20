<?php

// Vue : formulaire de contact pour les utilisateurs du site

$pageTitle = 'Contact - Vite & Gourmand';
require __DIR__ . '/../partials/header.php';
?>

<section class="form-shell">
    <div class="form-card">
        <p class="eyebrow">Contact</p>
        <h2>Parlons de votre événement</h2>

        <form method="post" action="index.php?page=contact_post" class="form-stack">
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
    </div>
    <div class="form-aside">
        <h3>Besoin d’un conseil ?</h3>
        <p>Nous répondons rapidement avec une proposition adaptée à votre budget et à votre timing.</p>
    </div>
</section>

<?php require __DIR__ . '/../partials/footer.php'; ?>
