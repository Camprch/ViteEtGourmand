<?php
// views/partials/header.php
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>
        <?= isset($pageTitle) ? htmlspecialchars($pageTitle) : 'Vite & Gourmand'; ?>
    </title>
</head>
<body>
    <header>
        <h1>Vite & Gourmand</h1>

        <nav>
            <a href="index.php?page=home">Accueil</a>
            <a href="index.php?page=menus">Nos menus</a>
            <a href="index.php?page=login">Connexion</a>
            <a href="index.php?page=register">Créer un compte</a>
            <a href="index.php?page=contact">Contact</a>
        </nav>
    </header>

    <main>
