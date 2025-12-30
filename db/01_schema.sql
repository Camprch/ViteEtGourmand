USE vite_gourmand;

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- =========================
-- TABLE user
-- =========================
CREATE TABLE IF NOT EXISTS `user` (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(100) NOT NULL,
    prenom VARCHAR(100) NOT NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    telephone VARCHAR(20) DEFAULT NULL,
    adresse VARCHAR(255) DEFAULT NULL,
    role ENUM('USER','EMPLOYE','ADMIN') NOT NULL DEFAULT 'USER',
    actif TINYINT(1) NOT NULL DEFAULT 1,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB
  DEFAULT CHARSET=utf8mb4
  COLLATE=utf8mb4_unicode_ci;

-- =========================
-- TABLE password_reset_token (mot de passe oublié)
-- =========================
CREATE TABLE IF NOT EXISTS password_reset_token (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    id_user INT UNSIGNED NOT NULL,
    token VARCHAR(64) NOT NULL UNIQUE,
    expires_at DATETIME NOT NULL,
    used TINYINT(1) NOT NULL DEFAULT 0,
    CONSTRAINT fk_password_reset_user
        FOREIGN KEY (id_user) REFERENCES `user`(id)
        ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB
  DEFAULT CHARSET=utf8mb4
  COLLATE=utf8mb4_unicode_ci;

-- =========================
-- TABLE menu
-- =========================
CREATE TABLE IF NOT EXISTS menu (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    titre VARCHAR(150) NOT NULL,
    description TEXT NOT NULL,
    theme VARCHAR(50) DEFAULT NULL,
    prix_par_personne DECIMAL(10,2) NOT NULL,
    personnes_min INT UNSIGNED NOT NULL,
    conditions_particulieres TEXT DEFAULT NULL,
    regime VARCHAR(50) DEFAULT NULL,
    stock INT UNSIGNED DEFAULT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB
  DEFAULT CHARSET=utf8mb4
  COLLATE=utf8mb4_unicode_ci;

-- =========================
-- TABLE menu_image (galerie d'images par menu)
-- =========================
CREATE TABLE IF NOT EXISTS menu_image (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    id_menu INT UNSIGNED NOT NULL,
    chemin VARCHAR(255) NOT NULL,
    alt_text VARCHAR(255) DEFAULT NULL,
    is_principale TINYINT(1) NOT NULL DEFAULT 0,
    CONSTRAINT fk_menu_image_menu
        FOREIGN KEY (id_menu) REFERENCES menu(id)
        ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB
  DEFAULT CHARSET=utf8mb4
  COLLATE=utf8mb4_unicode_ci;

-- =========================
-- TABLE plat
-- =========================
CREATE TABLE IF NOT EXISTS plat (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(150) NOT NULL,
    description TEXT DEFAULT NULL,
    type ENUM('ENTREE','PLAT','DESSERT') NOT NULL
) ENGINE=InnoDB
  DEFAULT CHARSET=utf8mb4
  COLLATE=utf8mb4_unicode_ci;

-- =========================
-- TABLE allergene
-- =========================
CREATE TABLE IF NOT EXISTS allergene (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(80) NOT NULL UNIQUE
) ENGINE=InnoDB
  DEFAULT CHARSET=utf8mb4
  COLLATE=utf8mb4_unicode_ci;

-- =========================
-- TABLE commande
-- =========================
CREATE TABLE IF NOT EXISTS commande (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    id_user INT UNSIGNED NOT NULL,
    id_menu INT UNSIGNED NOT NULL,
    date_commande DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    date_prestation DATE NOT NULL,
    heure_prestation TIME NOT NULL,
    adresse_prestation VARCHAR(255) NOT NULL,
    ville VARCHAR(100) NOT NULL,
    code_postal VARCHAR(10) NOT NULL,
    nb_personnes INT UNSIGNED NOT NULL,
    prix_menu_total DECIMAL(10,2) NOT NULL,
    reduction_appliquee DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    frais_livraison DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    prix_total DECIMAL(10,2) NOT NULL,
    statut_courant ENUM(
        'EN_ATTENTE',
        'ACCEPTEE',
        'EN_PREPARATION',
        'EN_LIVRAISON',
        'LIVREE',
        'ATTENTE_RETOUR_MATERIEL',
        'TERMINEE',
        'ANNULEE'
    ) NOT NULL DEFAULT 'EN_ATTENTE',
    CONSTRAINT fk_commande_user
        FOREIGN KEY (id_user) REFERENCES `user`(id)
        ON DELETE RESTRICT ON UPDATE CASCADE,
    CONSTRAINT fk_commande_menu
        FOREIGN KEY (id_menu) REFERENCES menu(id)
        ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB
  DEFAULT CHARSET=utf8mb4
  COLLATE=utf8mb4_unicode_ci;

-- =========================
-- TABLE horaire
-- =========================
CREATE TABLE IF NOT EXISTS horaire (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    jour VARCHAR(20) NOT NULL,
    heure_ouverture TIME DEFAULT NULL,
    heure_fermeture TIME DEFAULT NULL,
    ferme TINYINT(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB
  DEFAULT CHARSET=utf8mb4
  COLLATE=utf8mb4_unicode_ci;

-- =========================
-- TABLE contact_message
-- =========================
CREATE TABLE IF NOT EXISTS contact_message (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(100) NOT NULL,
    email VARCHAR(150) NOT NULL,
    titre VARCHAR(150) NOT NULL,
    message TEXT NOT NULL,
    date DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    traite TINYINT(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB
  DEFAULT CHARSET=utf8mb4
  COLLATE=utf8mb4_unicode_ci;

-- =========================
-- TABLE menu_plat (relation N-N)
-- =========================
CREATE TABLE IF NOT EXISTS menu_plat (
    id_menu INT UNSIGNED NOT NULL,
    id_plat INT UNSIGNED NOT NULL,
    ordre INT UNSIGNED DEFAULT NULL,
    PRIMARY KEY (id_menu, id_plat),
    CONSTRAINT fk_menu_plat_menu
        FOREIGN KEY (id_menu) REFERENCES menu(id)
        ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT fk_menu_plat_plat
        FOREIGN KEY (id_plat) REFERENCES plat(id)
        ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB
  DEFAULT CHARSET=utf8mb4
  COLLATE=utf8mb4_unicode_ci;

-- =========================
-- TABLE plat_allergene (relation N-N)
-- =========================
CREATE TABLE IF NOT EXISTS plat_allergene (
    id_plat INT UNSIGNED NOT NULL,
    id_allergene INT UNSIGNED NOT NULL,
    PRIMARY KEY (id_plat, id_allergene),
    CONSTRAINT fk_plat_allergene_plat
        FOREIGN KEY (id_plat) REFERENCES plat(id)
        ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT fk_plat_allergene_allergene
        FOREIGN KEY (id_allergene) REFERENCES allergene(id)
        ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB
  DEFAULT CHARSET=utf8mb4
  COLLATE=utf8mb4_unicode_ci;

-- =========================
-- TABLE commande_statut (historique)
-- =========================
CREATE TABLE IF NOT EXISTS commande_statut (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    id_commande INT UNSIGNED NOT NULL,
    id_employe INT UNSIGNED NULL,
    statut ENUM(
        'EN_ATTENTE',
        'ACCEPTEE',
        'EN_PREPARATION',
        'EN_LIVRAISON',
        'LIVREE',
        'ATTENTE_RETOUR_MATERIEL',
        'TERMINEE',
        'ANNULEE'
    ) NOT NULL,
    date_heure DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    commentaire TEXT DEFAULT NULL,
    CONSTRAINT fk_commande_statut_commande
        FOREIGN KEY (id_commande) REFERENCES commande(id)
        ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT fk_commande_statut_employe
        FOREIGN KEY (id_employe) REFERENCES `user`(id)
        ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB
  DEFAULT CHARSET=utf8mb4
  COLLATE=utf8mb4_unicode_ci;

-- =========================
-- TABLE avis
-- =========================
CREATE TABLE IF NOT EXISTS avis (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    id_commande INT UNSIGNED NOT NULL,
    id_user INT UNSIGNED NOT NULL,
    id_menu INT UNSIGNED NOT NULL,
    note TINYINT UNSIGNED NOT NULL,
    commentaire TEXT DEFAULT NULL,
    date DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    valide TINYINT(1) NOT NULL DEFAULT 0,
    CONSTRAINT fk_avis_commande
        FOREIGN KEY (id_commande) REFERENCES commande(id)
        ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT fk_avis_user
        FOREIGN KEY (id_user) REFERENCES `user`(id)
        ON DELETE RESTRICT ON UPDATE CASCADE,
    CONSTRAINT fk_avis_menu
        FOREIGN KEY (id_menu) REFERENCES menu(id)
        ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB
  DEFAULT CHARSET=utf8mb4
  COLLATE=utf8mb4_unicode_ci;

SET FOREIGN_KEY_CHECKS = 1;
