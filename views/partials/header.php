<?php

// Vue : affichage de l'en-tête du site

// Utilisé par : toutes les vues du site

// Raccourci pratique pour savoir si on est connecté
$user = $_SESSION['user'] ?? null;

$pageSlug = $_GET['page'] ?? 'home';
$defaultDescription = 'Traiteur local : menus sur mesure pour vos repas de famille et événements professionnels.';
$pageDescription = $pageDescription ?? $defaultDescription;

$publicPages = ['home', 'menus', 'menu', 'contact', 'mentions_legales', 'rgpd', 'cgv'];
$pageRobots = in_array($pageSlug, $publicPages, true) ? 'index,follow' : 'noindex,nofollow';

$baseUrl = rtrim((string)getenv('APP_URL'), '/');
if ($baseUrl === '') {
    $scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
    $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
    $baseUrl = $scheme . '://' . $host;
}
$requestUri = $_SERVER['REQUEST_URI'] ?? '/';
$canonicalUrl = $baseUrl . $requestUri;
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="<?= htmlspecialchars($pageDescription) ?>">
    <meta name="robots" content="<?= htmlspecialchars($pageRobots) ?>">
    <link rel="canonical" href="<?= htmlspecialchars($canonicalUrl) ?>">
    <meta property="og:site_name" content="Vite & Gourmand">
    <meta property="og:type" content="website">
    <meta property="og:title" content="<?= isset($pageTitle) ? htmlspecialchars($pageTitle) : 'Vite & Gourmand' ?>">
    <meta property="og:description" content="<?= htmlspecialchars($pageDescription) ?>">
    <meta property="og:url" content="<?= htmlspecialchars($canonicalUrl) ?>">
    <meta property="og:locale" content="fr_FR">
    <meta name="twitter:card" content="summary">
    <meta name="theme-color" content="#f7f2ea">
    <link rel="stylesheet" href="css/style.css">

    <title>
        <?= isset($pageTitle) ? htmlspecialchars($pageTitle) : 'Vite & Gourmand'; ?>
    </title>
    <!-- Favicon emoji 🍲 -->
    <link rel="icon" type="image/svg+xml"
          href="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 64 64'%3E%3Ctext x='50%25' y='50%25' text-anchor='middle' dominant-baseline='central' font-size='52'%3E🍲%3C/text%3E%3C/svg%3E">
</head>

<body class="site-body">

<a class="skip-link" href="#main-content">Aller au contenu</a>

<!-- En-tête du site avec navigation principale -->
<header class="site-header">
    <div class="container header-inner">
        <div class="site-brand">
            <h1 class="site-title">
                <a class="site-logo" href="index.php?page=home" aria-label="Retour à l'accueil">
                    <span class="site-logo-emoji" aria-hidden="true">🍲</span> Vite & Gourmand
                </a>
            </h1>
            <?php if ($user): ?>
                <p class="site-greeting">Bonjour <?= htmlspecialchars($user['prenom']) ?></p>
            <?php endif; ?>
        </div>

        <nav class="site-nav" aria-label="Navigation principale">
            <div class="nav-primary">
                <a href="index.php?page=home">Accueil</a>
                <a href="index.php?page=menus">Nos menus</a>
                <a href="index.php?page=contact">Contact</a>
            </div>

            <div class="nav-secondary">
                <?php if ($user): ?>
                    <a href="index.php?page=mes_commandes">Mes commandes</a>

                    <details class="nav-menu">
                        <summary>Mon compte</summary>
                        <div class="nav-menu-panel">
                            <a href="index.php?page=profil">Mon profil</a>
                            <?php if (in_array($user['role'], ['EMPLOYE','ADMIN'], true)): ?>
                                <a href="index.php?page=dashboard_employe">Espace employé</a>
                            <?php endif; ?>
                            <?php if ($user['role'] === 'ADMIN'): ?>
                                <a href="index.php?page=dashboard_admin">Administration</a>
                            <?php endif; ?>
                            <a href="index.php?page=logout">Déconnexion</a>
                        </div>
                    </details>
                <?php else: ?>
                    <a href="index.php?page=login">Connexion</a>
                    <a href="index.php?page=register">Créer un compte</a>
                <?php endif; ?>
            </div>
        </nav>
    </div>
</header>

<!-- Début du contenu principal de la page -->
<main class="site-main" id="main-content">
    <div class="container">
