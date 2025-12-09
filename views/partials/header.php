<?php
// views/partials/header.php

// Démarre la session si pas déjà démarrée
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Raccourci pratique pour savoir si on est connecté
$user = $_SESSION['user'] ?? null;
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">

    <title>
        <?= isset($pageTitle) ? htmlspecialchars($pageTitle) : 'Vite & Gourmand'; ?>
    </title>

    <style>
        /* Mini style pour que ce soit lisible (tu pourras remplacer par ton CSS ensuite) */
        header {
            background: #f5f5f5;
            padding: 15px;
            margin-bottom: 25px;
            border-bottom: 1px solid #ccc;
        }

        nav a {
            margin-right: 15px;
            text-decoration: none;
        }

        nav span {
            margin-right: 15px;
            font-weight: bold;
        }
    </style>
</head>

<body>

<header>
    <h1>Vite & Gourmand</h1>

    <nav>
        <!-- Liens accessibles à tout le monde -->
        <a href="index.php?page=home">Accueil</a>
        <a href="index.php?page=menus">Nos menus</a>
        <a href="index.php?page=contact">Contact</a>

        <?php if ($user): ?>
            <!-- Zone utilisateur connecté -->
            <span>
                Bonjour <?= htmlspecialchars($user['prenom']) ?> (<?= htmlspecialchars($user['role']) ?>)
            </span>

            <a href="index.php?page=mes_commandes">Mes commandes</a>

            <?php if ($user['role'] === 'EMPLOYE'): ?>
                <a href="index.php?page=dashboard_employe">Espace employé</a>
            <?php endif; ?>

            <?php if ($user['role'] === 'ADMIN'): ?>
                <a href="index.php?page=dashboard_admin">Administration</a>
            <?php endif; ?>

            <a href="index.php?page=logout">Déconnexion</a>

        <?php else: ?>
            <!-- Zone visiteur -->
            <a href="index.php?page=login">Connexion</a>
            <a href="index.php?page=register">Créer un compte</a>
        <?php endif; ?>
    </nav>
</header>

<main>
