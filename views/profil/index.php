<?php

// Vue : affichage de la page profil

// Utilisé par : route page=profil
require __DIR__ . '/../partials/header.php';
?>


<section class="page-head">
    <div>
        <p class="eyebrow">Compte</p>
        <h1>Mon profil</h1>
        <p class="muted">Mettez à jour vos informations et votre mot de passe.</p>
    </div>
</section>


<!-- Messages de confirmation après modification -->
<?php if (isset($_GET['updated'])): ?><section class="notice">✅ Profil mis à jour</section><?php endif; ?>
<?php if (isset($_GET['password_updated'])): ?><section class="notice">✅ Mot de passe modifié</section><?php endif; ?>


<section class="profile-grid">
    <div class="card">
        <h2>Informations</h2>
        <form method="post" action="index.php?page=profil_update" class="form-grid">
            <input type="hidden" name="_csrf" value="<?= htmlspecialchars(Csrf::token()) ?>">

            <label>Prénom
                <input name="prenom" value="<?= htmlspecialchars($profileUser['prenom']) ?>" required>
            </label>

            <label>Nom
                <input name="nom" value="<?= htmlspecialchars($profileUser['nom']) ?>" required>
            </label>

            <label>Email
                <input type="email" name="email" value="<?= htmlspecialchars($profileUser['email']) ?>" required>
            </label>

            <label>Téléphone
                <input name="telephone" value="<?= htmlspecialchars($profileUser['telephone'] ?? '') ?>">
            </label>

            <label class="span-2">Adresse
                <input name="adresse" value="<?= htmlspecialchars($profileUser['adresse'] ?? '') ?>">
            </label>

            <div class="form-actions span-2">
                <button type="submit">Enregistrer</button>
            </div>
        </form>
    </div>

    <div class="card">
        <h2>Changer le mot de passe</h2>
        <form method="post" action="index.php?page=profil_password" class="form-stack">
            <input type="hidden" name="_csrf" value="<?= htmlspecialchars(Csrf::token()) ?>">

            <label>Ancien mot de passe
                <input type="password" name="old_password" required>
            </label>

            <label>Nouveau mot de passe
                <input type="password" name="new_password" required>
            </label>

            <label>Confirmation
                <input type="password" name="confirm_password" required>
            </label>

            <button type="submit">Mettre à jour</button>
        </form>
    </div>
</section>


<?php require __DIR__ . '/../partials/footer.php'; ?>
