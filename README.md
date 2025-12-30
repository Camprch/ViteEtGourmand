# 🍲 Vite & Gourmand

Vite & Gourmand est une application web de gestion de commandes pour un service traiteur.

---

## 🎯 Fonctionnalités principales

### Front (utilisateur)
- Inscription, connexion, réinitialisation du mot de passe (email)
- Consultation des menus et plats
- Passage de commandes en ligne
- Gestion du profil utilisateur
- Dépôt d’avis clients
- Formulaire de contact

### Back-office (admin et employé)
- Gestion des menus (CRUD, activation/désactivation, images)
- Gestion des plats et allergènes
- Association menus ↔ plats ↔ allergènes
- Gestion des commandes (statuts, historique)
- Gestion des employés (ADMIN)
- Statistiques de ventes (ADMIN)

---

## 👥 Rôles utilisateurs

- **USER** : client final
- **EMPLOYE** : gestion des commandes, menus, plats
- **ADMIN** : gestion globale + statistiques

Les accès sont contrôlés côté serveur selon le rôle.

---

## 📊 Statistiques (NoSQL – MongoDB)

MongoDB est utilisé pour les statistiques :
- Les commandes sont enregistrées dans MongoDB **au moment où elles passent au statut `ACCEPTEE`**
- Calculs via agrégations MongoDB :
  - Nombre de commandes
  - Chiffre d’affaires total
  - Chiffre d’affaires par menu
- Filtrage par période (dates)

👉 SQL = base transactionnelle  
👉 MongoDB = base analytique (statistiques)

---

## 📧 Emails

Envoi d’emails réels via SMTP :
- Réinitialisation du mot de passe
- Notifications liées aux commandes

Configuration via `.env`.

---

## 🏗️ Structure du projet

- `public/` : Fichiers accessibles publiquement (index et image upload)
- `src/config/` : Fichiers de configuration (env et db)
- `src/controller/` : Contrôleurs (logique métier)
- `src/model/` : Modèles (accès aux données)
- `views/` : Vues (pages affichées)
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

Accès via : 👉 http://localhost:8000

---

⚙️ Prérequis

- PHP 8.x
- MySQL ou MariaDB
- MongoDB
- Extension PHP MongoDB
- Composer

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
