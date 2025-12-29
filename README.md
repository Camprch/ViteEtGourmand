# 🍲 Vite & Gourmand

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

1. Clôner le repo

```bash
git clone https://github.com/Camprch/vite-gourmand
```
2. Configurer l'accès à la base de données dans vite-gourmand/.env  
(modifie les valeurs de DB_DSN, DB_USER, DB_PASS selon ta config)

3. Importer le schema dans la db.

```bash
mysql -u vg_user -p vite_gourmand < sql/schema.sql
```

---

## 🚀 Lancement

```bash
cd vite-gourmand
php -S localhost:8000 -t public
```
Accéder à l'application via `http://localhost/vite-gourmand/public`

---

## ⚙️ Prérequis

- PHP 8.x ou supérieur
- MySQL/MariaDB

---

## 🔐 Identifiants de test

- Compte ADMIN :  
Email : admin@vite-gourmand.local   
Mot de passe : Admin12345!   

- Compte EMPLOYÉ :  
Email : employe@vite-gourmand.local   
Mot de passe : Employe123!  

- Compte Utilisateur :  
Email : utilisateur@vite-gourmand.local  
Mot de passe : Utilisateur123!  
