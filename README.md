# 🍲 Vite & Gourmand

Vite & Gourmand est une application web de gestion de commandes pour un service traiteur.

![Accueil](public/uploads/screenshot/accueil.png)
![Stats](public/uploads/screenshot/stats.png)

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

SQL = base transactionnelle  
MongoDB = base analytique (statistiques)

### Seed MongoDB (statistiques)

Le graphique lit la collection MongoDB `orders_stats`.  
Pour générer rapidement des données réalistes (volume sur 6 mois), utilise le script :

```bash
./scripts/seed-mongo.sh
```

Options (variables d'environnement) :
```bash
COUNT=400 DAYS=240 ./scripts/seed-mongo.sh
```

Le script utilise les variables `.env` suivantes :
- `MONGO_INITDB_ROOT_USERNAME`
- `MONGO_INITDB_ROOT_PASSWORD`
- `MONGO_DB`

---

## 📧 Emails

Envoi d’emails réels via SMTP :
- Réinitialisation du mot de passe
- Notifications liées aux commandes

Configuration via `.env` avec `MAIL_FROM_EMAIL`, `MAIL_FROM_NAME` et `MAILER_DSN` (ex. SMTP Brevo comme dans `.env.example`).

---

## 🏗️ Structure du projet

- `public/` : Fichiers accessibles publiquement (index et image upload)
- `src/config/` : Fichiers de configuration (env et db)
- `src/controller/` : Contrôleurs (logique métier)
- `src/model/` : Modèles (accès aux données)
- `views/` : Vues (pages affichées)
- `db/` : Scripts SQL exécutés automatiquement par MariaDB (schema + données de démonstration)
- `sql/` : Scripts SQL de référence (lecture / documentation)

---

## 🗄️ Base de données : scripts SQL

Deux fichiers de schéma coexistent volontairement :
- `db/01_schema.sql` : utilisé par **Docker/MariaDB** pour l’initialisation automatique en local.
- `sql/schema.sql` : **référence de schéma** pour déploiement / prod / usage manuel.

Conseil d’usage :
- Modifie d’abord `sql/schema.sql`, puis reporte la modification dans `db/01_schema.sql` si tu veux garder l’init Docker à jour.

---

## 💾 Installation

### Prérequis
- Docker
- Docker Compose
- PHP 8.x
- Extension PHP MongoDB
- Composer

### 1. Cloner le dépôt

```bash
git clone https://github.com/Camprch/vite-gourmand.git
cd vite-gourmand
```

### 2. Configuration

Créer le fichier .env à partir de l’exemple :
```bash
cp .env.example .env
```

### 3. Démarrer les bases de données (initialisation automatique)

Les bases de données sont initialisées automatiquement via Docker :
- MariaDB (schéma + données de démonstration)
- MongoDB (statistiques)

```bash
docker-compose up -d
```
### 4. Lancer l'application

```bash
php -S localhost:8000 -t public
```

Accès via : [http://localhost:8000](http://localhost:8000)

### 5. (Optionnel) Réinitialiser les bases de données.

⚠️ Cette commande supprime toutes les données et rejoue automatiquement le schéma et les données de démonstration.
```bash
docker-compose down -v
docker-compose up -d
```

---

## 🔐 Identifiants de test

- Compte ADMIN :  
Email : admin@vitegourmand.local   
Mot de passe : Admin12345!   

- Compte EMPLOYÉ :  
Email : employe@vitegourmand.local   
Mot de passe : Employe123!  

- Compte Utilisateur :  
Email : user@vitegourmand.local  
Mot de passe : Utilisateur123!  
