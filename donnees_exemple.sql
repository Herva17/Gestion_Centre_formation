-- ============================================
-- Données d'exemple pour tester l'application
-- ============================================

-- Insérer des apprenants d'exemple
INSERT INTO `apprenant` (`nom`, `prenom`, `telephone`, `adresse`) VALUES
('Diallo', 'Amadou', '77123456', 'Dakar - Plateau'),
('Ba', 'Fatou', '78234567', 'Dakar - Medina'),
('Ndiaye', 'Ousmane', '77345678', 'Dakar - Sacré-Coeur'),
('Sow', 'Aissatou', '77456789', 'Dakar - Fann'),
('Cissé', 'Moussa', '78567890', 'Dakar - Point E'),
('Thiam', 'Mariama', '77678901', 'Dakar - Mermoz'),
('Diouf', 'Ibrahima', '78789012', 'Dakar - Yoff'),
('Sarr', 'Hawa', '77890123', 'Dakar - Ngor'),
('Fall', 'Cheikh', '78901234', 'Dakar - Parcelles'),
('Gueye', 'Awa', '77012345', 'Dakar - Liberté');

-- Insérer des filières d'exemple
INSERT INTO `filiere` (`nom`, `duree`, `frais_mensuel`) VALUES
('Informatique Web & Mobile', '6 mois', 150000),
('Développement Full Stack', '8 mois', 180000),
('Data Science et IA', '6 mois', 200000),
('Cybersécurité', '6 mois', 190000),
('UX/UI Design', '4 mois', 140000),
('Cloud Computing', '5 mois', 160000),
('DevOps & Infrastructure', '6 mois', 170000),
('Marketing Digital', '3 mois', 120000);

-- Insérer des salles d'exemple
INSERT INTO `salle` (`nom`, `capacite`) VALUES
('Salle A - Bloc 1', 30),
('Salle B - Bloc 1', 35),
('Salle C - Bloc 2', 40),
('Salle D - Bloc 2', 25),
('Labo Informatique 1', 20),
('Labo Informatique 2', 25),
('Amphithéâtre Principal', 100),
('Salle de Conférence', 50);

-- Insérer des cours d'exemple
INSERT INTO `cours` (`nom`, `description`, `id_filiere`) VALUES
('HTML5 & CSS3', 'Maîtrise les bases du web moderne', 1),
('JavaScript Avancé', 'Programmation JavaScript côté client et serveur', 1),
('React.js', 'Framework JavaScript pour interfaces dynamiques', 2),
('Node.js', 'Environnement JavaScript côté serveur', 2),
('Python pour Data Science', 'Introduction à Python et aux bibliothèques de data science', 3),
('Machine Learning', 'Algorithmes et modèles de machine learning', 3),
('Principes de Sécurité', 'Fondamentaux de la cybersécurité', 4),
('Ethical Hacking', 'Techniques de test de pénétration', 4),
('UI/UX Design Basics', 'Principes fondamentaux du design', 5),
('Figma & Prototypage', 'Conception d''interfaces avec Figma', 5);

-- Insérer des horaires d'exemple
INSERT INTO `horaire` (`jour`, `heure_debut`, `heure_fin`, `id_salle`, `id_cours`) VALUES
('Lundi', '08:00:00', '10:00:00', 1, 1),
('Lundi', '14:00:00', '16:00:00', 5, 3),
('Mardi', '08:00:00', '10:00:00', 2, 2),
('Mardi', '14:00:00', '16:00:00', 6, 4),
('Mercredi', '09:00:00', '11:00:00', 3, 5),
('Mercredi', '14:00:00', '16:00:00', 4, 7),
('Jeudi', '08:00:00', '10:00:00', 1, 8),
('Jeudi', '14:00:00', '16:00:00', 2, 9),
('Vendredi', '09:00:00', '11:00:00', 3, 10),
('Samedi', '10:00:00', '12:00:00', 7, 1);

-- Insérer des inscriptions d'exemple
INSERT INTO `inscription` (`date_inscription`, `frais_inscription`, `id_apprenant`, `id_filiere`) VALUES
('2024-01-15', 500000, 1, 1),
('2024-01-18', 600000, 2, 2),
('2024-02-01', 550000, 3, 3),
('2024-02-10', 500000, 4, 4),
('2024-02-20', 450000, 5, 5),
('2024-03-05', 500000, 6, 1),
('2024-03-12', 600000, 7, 2),
('2024-03-20', 550000, 8, 6),
('2024-04-01', 500000, 9, 7),
('2024-04-15', 450000, 10, 5);

-- Insérer des paiements d'exemple
INSERT INTO `paiement` (`montant`, `type`, `mois`, `id_inscription`) VALUES
(250000, 'Espèces', 'Janvier', 1),
(250000, 'Virement', 'Février', 1),
(300000, 'Chèque', 'Janvier', 2),
(300000, 'Carte', 'Février', 2),
(275000, 'Espèces', 'Février', 3),
(275000, 'Virement', 'Mars', 3),
(250000, 'Chèque', 'Février', 4),
(250000, 'Espèces', 'Mars', 4),
(225000, 'Virement', 'Mars', 5),
(225000, 'Carte', 'Avril', 5),
(250000, 'Espèces', 'Mars', 6),
(250000, 'Chèque', 'Avril', 6),
(300000, 'Virement', 'Mars', 7),
(300000, 'Espèces', 'Avril', 7),
(275000, 'Carte', 'Avril', 8),
(275000, 'Virement', 'Mai', 8),
(250000, 'Espèces', 'Avril', 9),
(250000, 'Chèque', 'Mai', 9),
(225000, 'Virement', 'Avril', 10),
(225000, 'Carte', 'Mai', 10);

-- ============================================
-- Fin des données d'exemple
-- ============================================
-- 
-- Notes:
-- - 10 apprenants
-- - 8 filières
-- - 8 salles
-- - 10 cours
-- - 10 horaires
-- - 10 inscriptions
-- - 20 paiements
--
-- Cette base de données de test permet de:
-- ✓ Visualiser des données réalistes
-- ✓ Tester tous les modules
-- ✓ Vérifier les graphiques et statistiques
-- ✓ Démontrer les fonctionnalités
-- ============================================
