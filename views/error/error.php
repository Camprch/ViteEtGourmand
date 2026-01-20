<?php

// Vue : page d'erreur générique
// Variables attendues : $errorCode, $errorTitle, $errorMessage

require __DIR__ . '/../partials/header.php';
?>

<section class="page-head">
    <div>
        <p class="eyebrow">Erreur <?= (int)$errorCode ?></p>
        <h2><?= htmlspecialchars($errorTitle) ?></h2>
        <p class="muted"><?= htmlspecialchars($errorMessage) ?></p>
    </div>
</section>

<section class="cta-bar">
    <a class="btn btn-ghost" href="index.php?page=home">Retour à l’accueil</a>
</section>

<?php require __DIR__ . '/../partials/footer.php'; ?>
