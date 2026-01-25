USE vite_gourmand;

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- Nettoyage
TRUNCATE TABLE avis;
TRUNCATE TABLE commande_statut;
TRUNCATE TABLE commande;
TRUNCATE TABLE plat_allergene;
TRUNCATE TABLE menu_plat;
TRUNCATE TABLE menu_image;
TRUNCATE TABLE allergene;
TRUNCATE TABLE plat;
TRUNCATE TABLE contact_message;
TRUNCATE TABLE horaire;
TRUNCATE TABLE password_reset_token;
TRUNCATE TABLE `user`;

-- =========================
-- USERS
-- =========================
-- MDP (bcrypt) :
-- Admin12345!       -> hash 1
-- Employe123!        -> hash 2
-- Utilisateur123!    -> hash 3
INSERT INTO `user` (id, nom, prenom, email, password, telephone, adresse, role, actif)
VALUES
(1, 'Admin', 'ViteGourmand', 'admin@vitegourmand.local',
 '$2b$10$kvZmpde61Hm3DjCUW3hfBOmNDt3yZ9yrZ7rG2oAlIk3ZRHRjLTLpm',
 '0600000001', '1 rue du Jury, 75000 Paris', 'ADMIN', 1),
(2, 'Employe', 'ViteGourmand', 'employe@vitegourmand.local',
 '$2b$10$k/N0Kmvw9yr5ZB.bFcWMFeJKZJ3Ap8lBxgCsmzUM3sGxw3KfnOpdC',
 '0600000002', '2 rue du Service, 75000 Paris', 'EMPLOYE', 1),
(3, 'User', 'ViteGourmand', 'user@vitegourmand.local',
 '$2b$10$H7rU8LMgb8OaeS13oFtSRuwBd856HnaaA/0X1rvEH95dEOh6Tsn1a',
 '0600000003', '3 rue du Client, 75000 Paris', 'USER', 1);

-- =========================
-- HORAIRES
-- =========================
INSERT INTO horaire (jour, heure_ouverture, heure_fermeture, ferme) VALUES
('Lundi',    '09:00:00', '18:00:00', 0),
('Mardi',    '09:00:00', '18:00:00', 0),
('Mercredi', '09:00:00', '18:00:00', 0),
('Jeudi',    '09:00:00', '18:00:00', 0),
('Vendredi', '09:00:00', '18:00:00', 0),
('Samedi',   '10:00:00', '16:00:00', 0),
('Dimanche', NULL,       NULL,       1);

-- =========================
-- MENUS + IMAGES
-- =========================
INSERT INTO menu (id, titre, description, theme, prix_par_personne, personnes_min, conditions_particulieres, regime, stock)
VALUES
(1, 'Salade végé', 'Salade croquante et colorée, légumes de saison et vinaigrette maison.', 'Végétarien', 19.50, 2,
 'Options sans gluten sur demande.', 'Végétarien', 25),
(2, 'Boeuf bourguignon', 'Boeuf mijoté au vin rouge, carottes et champignons.', 'Terroir', 26.90, 2,
 'Cuisson lente, prévoir un temps de préparation plus long.', 'Omnivore', 18),
(3, 'Choucroute', 'Choucroute garnie traditionnelle, charcuteries et pommes de terre.', 'Alsace', 25.90, 4,
 'Peut contenir de la moutarde.', 'Omnivore', 12),
(4, 'Poulet rôti', 'Poulet rôti aux herbes, frites.', 'Classique', 23.90, 2,
 'Possibilité de demander une cuisson bien cuite.', 'Omnivore', 30);

INSERT INTO menu_image (id_menu, chemin, alt_text, is_principale)
VALUES
(1, 'uploads/menus/Salade vege.png', 'Salade végé', 1),
(2, 'uploads/menus/boeuf bourguignon.png', 'Boeuf bourguignon', 1),
(3, 'uploads/menus/choucroute.png', 'Choucroute', 1),
(4, 'uploads/menus/poulet roti.png', 'Poulet rôti', 1);

-- =========================
-- PLATS
-- =========================
INSERT INTO plat (id, nom, description, type) VALUES
(1, 'Salade de saison', 'Jeunes pousses, légumes croquants, vinaigrette maison.', 'ENTREE'),
(2, 'Tarte légumes rôtis', 'Tarte salée aux légumes et herbes fraîches.', 'PLAT'),
(3, 'Compote pommes-poires', 'Compote maison, légère et fruitée.', 'DESSERT'),
(4, 'Boeuf bourguignon', 'Boeuf mijoté, carottes, champignons, vin rouge.', 'PLAT'),
(5, 'Pommes de terre vapeur', 'Accompagnement simple et gourmand.', 'PLAT'),
(6, 'Choucroute garnie', 'Chou fermenté, charcuteries, pommes de terre.', 'PLAT'),
(7, 'Poulet rôti aux herbes', 'Poulet rôti, jus au thym.', 'PLAT'),
(8, 'Gâteau moelleux', 'Gâteau du jour, texture fondante.', 'DESSERT');

-- =========================
-- ALLERGENES
-- =========================
INSERT INTO allergene (id, nom) VALUES
(1, 'Gluten'),
(2, 'Lait');

-- =========================
-- LIAISONS Menu <-> Plat
-- =========================
-- Salade végé : entrée + plat + dessert
INSERT INTO menu_plat (id_menu, id_plat, ordre) VALUES
(1, 1, 1),
(1, 2, 2),
(1, 3, 3);

-- Boeuf bourguignon : plat + accompagnement + dessert
INSERT INTO menu_plat (id_menu, id_plat, ordre) VALUES
(2, 4, 1),
(2, 5, 2),
(2, 8, 3);

-- Choucroute : plat + dessert
INSERT INTO menu_plat (id_menu, id_plat, ordre) VALUES
(3, 6, 1),
(3, 8, 2);

-- Poulet rôti : plat + dessert
INSERT INTO menu_plat (id_menu, id_plat, ordre) VALUES
(4, 7, 1),
(4, 8, 2);

-- =========================
-- LIAISONS Plat <-> Allergene
-- =========================
-- Bruschetta : gluten
INSERT INTO plat_allergene (id_plat, id_allergene) VALUES (1, 1);

-- Carbonara : gluten + lait
INSERT INTO plat_allergene (id_plat, id_allergene) VALUES (2, 1), (2, 2);

-- Tiramisu : lait 
INSERT INTO plat_allergene (id_plat, id_allergene) VALUES (3, 2);

-- Bowl : rien
-- Mousse aquafaba : rien

SET FOREIGN_KEY_CHECKS = 1;
