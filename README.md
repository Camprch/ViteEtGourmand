# 🍲 Vite Gourmand

Vite Gourmand est une application web de gestion de commandes pour un service traiteur.

---

## 🎯 Fonctionnalités principales

- Gestion des utilisateurs (inscription, connexion, mot de passe oublié)
- Gestion des menus et affichage des plats
- Prise de commandes en ligne
- Gestion des avis clients
- Formulaire de contact

---

## 🏗️ Structure du projet

- `public/` : Fichiers accessibles publiquement (ex : index.php)
- `src/config/` : Fichiers de configuration (connexion à la base de données)
- `src/controller/` : Contrôleurs (logique métier)
- `src/model/` : Modèles (accès aux données)
- `views/` : Vues (pages affichées à l'utilisateur)
- `sql/` : Scripts SQL (structure de la base de données)

---

## 💾 Installation

1. Cloner le dépôt
2. Importer le fichier `sql/schema.sql` dans votre base de données
3. Configurer l'accès à la base de données dans `src/config/db.php`
4. Placer le projet dans un serveur web local
5. Accéder à l'application via `http://localhost/vite-gourmand/public`

---

## ⚙️ Prérequis

- PHP 8.x ou supérieur
- Serveur web (Apache, Nginx...)
- MySQL/MariaDB

---

## ⌨ CMD

- Bash :
cd dev/vite-gourmand
php -S localhost:8000 -t public

- SQL : http://localhost/vite-gourmand/public/adminer.php
UPDATE user
SET role = 'ADMIN' or 'USER' or 'EMPLOYE'
WHERE email = 'email@exemple.com';
