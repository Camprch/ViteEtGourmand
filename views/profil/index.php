<?php

// Vue : affichage de la page profil

// Utilisé par : route page=profil
require __DIR__ . '/../partials/header.php';
?>


<!-- Titre de la page -->
<h1>Mon profil</h1>


<!-- Messages de confirmation après modification -->
<?php if (isset($_GET['updated'])): ?><p>✅ Profil mis à jour</p><?php endif; ?>
<?php if (isset($_GET['password_updated'])): ?><p>✅ Mot de passe modifié</p><?php endif; ?>


<!-- Formulaire de modification des informations du profil -->
<h2>Informations</h2>
<form method="post" action="index.php?page=profil_update">
    <input type="hidden" name="_csrf" value="<?= htmlspecialchars(Csrf::token()) ?>">

    <div>
        <label>Prénom</label><br>
        <input name="prenom" value="<?= htmlspecialchars($profileUser['prenom']) ?>" required>
    </div>

    <div>
        <label>Nom</label><br>
        <input name="nom" value="<?= htmlspecialchars($profileUser['nom']) ?>" required>
    </div>

    <div>
        <label>Email</label><br>
        <input type="email" name="email" value="<?= htmlspecialchars($profileUser['email']) ?>" required>
    </div>

    <div>
        <label>Téléphone</label><br>
        <input name="telephone" value="<?= htmlspecialchars($profileUser['telephone'] ?? '') ?>">
    </div>

    <div>
        <label>Adresse</label><br>
        <input name="adresse" value="<?= htmlspecialchars($profileUser['adresse'] ?? '') ?>">
    </div>

    <button type="submit">Enregistrer</button>
</form>


<hr>


<!-- Formulaire de changement de mot de passe -->
<h2>Changer le mot de passe</h2>
<form method="post" action="index.php?page=profil_password">
    <input type="hidden" name="_csrf" value="<?= htmlspecialchars(Csrf::token()) ?>">

    <div>
        <label>Ancien mot de passe</label><br>
        <input type="password" name="old_password" required>
    </div>

    <div>
        <label>Nouveau mot de passe</label><br>
        <input type="password" name="new_password" required>
    </div>

    <div>
        <label>Confirmation</label><br>
        <input type="password" name="confirm_password" required>
    </div>

    <button type="submit">Mettre à jour</button>
</form>


<?php require __DIR__ . '/../partials/footer.php'; ?>
