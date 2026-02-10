-- Initialisation de la base de données PostgreSQL pour Garage Auto

-- Création de la base de données si elle n'existe pas
SELECT 'CREATE DATABASE garage_auto'
WHERE NOT EXISTS (SELECT FROM pg_database WHERE datname = 'garage_auto')\gexec

-- Connexion à la base de données
\c garage_auto;

-- Table des utilisateurs
CREATE TABLE IF NOT EXISTS users (
    user_id SERIAL PRIMARY KEY,
    nom VARCHAR(100) NOT NULL,
    prenom VARCHAR(100) NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    mot_de_passe_hash VARCHAR(255) NOT NULL,
    telephone VARCHAR(20),
    adresse TEXT,
    date_inscription TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    role VARCHAR(50) DEFAULT 'client' CHECK (role IN ('client', 'admin')),
    firebase_uid VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Table des voitures
CREATE TABLE IF NOT EXISTS voitures (
    voiture_id SERIAL PRIMARY KEY,
    user_id INT NOT NULL,
    immatriculation VARCHAR(20) UNIQUE NOT NULL,
    marque VARCHAR(50) NOT NULL,
    modele VARCHAR(50) NOT NULL,
    annee INT,
    couleur VARCHAR(30),
    date_ajout TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    statut VARCHAR(50) DEFAULT 'en_attente' 
        CHECK (statut IN ('en_attente', 'en_reparation', 'prete', 'recuperee')),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE
);

-- Table des types d'intervention (8 types fixes)
CREATE TABLE IF NOT EXISTS types_intervention (
    type_id SERIAL PRIMARY KEY,
    nom VARCHAR(50) UNIQUE NOT NULL,
    duree_secondes INT NOT NULL,
    prix_unitaire DECIMAL(10,2) NOT NULL,
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Table des interventions signalées
CREATE TABLE IF NOT EXISTS interventions (
    intervention_id SERIAL PRIMARY KEY,
    voiture_id INT NOT NULL,
    type_id INT NOT NULL,
    description_panne TEXT,
    priorite VARCHAR(10) DEFAULT 'moyen' 
        CHECK (priorite IN ('faible', 'moyen', 'eleve')),
    date_signalement TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (voiture_id) REFERENCES voitures(voiture_id) ON DELETE CASCADE,
    FOREIGN KEY (type_id) REFERENCES types_intervention(type_id)
);

-- Table des réparations en cours
CREATE TABLE IF NOT EXISTS reparations_en_cours (
    reparation_id SERIAL PRIMARY KEY,
    voiture_id INT NOT NULL,
    type_id INT NOT NULL,
    slot_garage INT CHECK (slot_garage IN (1, 2)),
    date_debut TIMESTAMP,
    date_fin_estimee TIMESTAMP,
    statut VARCHAR(50) DEFAULT 'en_attente' 
        CHECK (statut IN ('en_attente', 'en_cours', 'terminee')),
    progression INT DEFAULT 0 CHECK (progression BETWEEN 0 AND 100),
    technicien_id INT,
    slot_attente BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (voiture_id) REFERENCES voitures(voiture_id) ON DELETE CASCADE,
    FOREIGN KEY (type_id) REFERENCES types_intervention(type_id),
    FOREIGN KEY (technicien_id) REFERENCES users(user_id)
);

-- Table des commandes
CREATE TABLE IF NOT EXISTS commandes (
    commande_id SERIAL PRIMARY KEY,
    user_id INT NOT NULL,
    voiture_id INT NOT NULL,
    date_commande TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    montant_total DECIMAL(10,2),
    statut_paiement VARCHAR(50) DEFAULT 'en_attente' 
        CHECK (statut_paiement IN ('en_attente', 'paye', 'echoue')),
    mode_paiement VARCHAR(50),
    date_paiement TIMESTAMP,
    transaction_id VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(user_id),
    FOREIGN KEY (voiture_id) REFERENCES voitures(voiture_id)
);

-- Table des détails de commande
CREATE TABLE IF NOT EXISTS commande_details (
    detail_id SERIAL PRIMARY KEY,
    commande_id INT NOT NULL,
    reparation_id INT NOT NULL,
    type_id INT NOT NULL,
    quantite INT DEFAULT 1,
    prix_unitaire DECIMAL(10,2),
    sous_total DECIMAL(10,2),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (commande_id) REFERENCES commandes(commande_id) ON DELETE CASCADE,
    FOREIGN KEY (reparation_id) REFERENCES reparations_en_cours(reparation_id),
    FOREIGN KEY (type_id) REFERENCES types_intervention(type_id)
);

-- Table des notifications
CREATE TABLE IF NOT EXISTS notifications (
    notification_id SERIAL PRIMARY KEY,
    user_id INT NOT NULL,
    titre VARCHAR(255) NOT NULL,
    message TEXT NOT NULL,
    type VARCHAR(50) NOT NULL 
        CHECK (type IN ('reparation_prete', 'paiement', 'mise_a_jour')),
    lue BOOLEAN DEFAULT FALSE,
    date_envoi TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    firebase_token TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE
);

-- Insérer les 8 types de réparations fixes
INSERT INTO types_intervention (nom, duree_secondes, prix_unitaire, description) 
VALUES 
('Frein', 3600, 150.00, 'Réparation du système de freinage'),
('Vidange', 1800, 80.00, 'Vidange d''huile et remplacement du filtre'),
('Filtre', 1200, 30.00, 'Remplacement des filtres'),
('Batterie', 2400, 120.00, 'Remplacement de la batterie'),
('Amortisseurs', 4800, 300.00, 'Remplacement des amortisseurs'),
('Embrayage', 7200, 450.00, 'Réparation du système d''embrayage'),
('Pneus', 3600, 200.00, 'Remplacement des pneus'),
('Système de refroidissement', 4200, 250.00, 'Réparation du système de refroidissement')
ON CONFLICT (nom) DO NOTHING;

-- Création des index pour améliorer les performances
CREATE INDEX IF NOT EXISTS idx_voitures_user_id ON voitures(user_id);
CREATE INDEX IF NOT EXISTS idx_voitures_statut ON voitures(statut);
CREATE INDEX IF NOT EXISTS idx_reparations_voiture_id ON reparations_en_cours(voiture_id);
CREATE INDEX IF NOT EXISTS idx_reparations_statut ON reparations_en_cours(statut);
CREATE INDEX IF NOT EXISTS idx_reparations_slot ON reparations_en_cours(slot_garage);
CREATE INDEX IF NOT EXISTS idx_interventions_voiture_id ON interventions(voiture_id);
CREATE INDEX IF NOT EXISTS idx_notifications_user_id ON notifications(user_id);
CREATE INDEX IF NOT EXISTS idx_notifications_lue ON notifications(lue);
CREATE INDEX IF NOT EXISTS idx_commandes_user_id ON commandes(user_id);
CREATE INDEX IF NOT EXISTS idx_commandes_statut ON commandes(statut_paiement);