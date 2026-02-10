-- Tena izy

-- Création de la base de données
CREATE DATABASE garage_auto;
\c garage_auto;

-- Table des utilisateurs
CREATE TABLE users (
    user_id SERIAL PRIMARY KEY,
    nom VARCHAR(100) NOT NULL,
    prenom VARCHAR(100) NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    mot_de_passe_hash VARCHAR(255) NOT NULL,
    telephone VARCHAR(20),
    adresse TEXT,
    date_inscription TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    role VARCHAR(50) DEFAULT 'client' CHECK (role IN ('client', 'admin')),
    firebase_uid VARCHAR(255)
);

-- Table des voitures
CREATE TABLE voitures (
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
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE
);

-- Table des types d'intervention (8 types fixes)
CREATE TABLE types_intervention (
    type_id SERIAL PRIMARY KEY,
    nom VARCHAR(50) UNIQUE NOT NULL,
    duree_secondes INT NOT NULL,
    prix_unitaire DECIMAL(10,2) NOT NULL,
    description TEXT
);

-- Insérer les 8 types de réparations fixes
INSERT INTO types_intervention (nom, duree_secondes, prix_unitaire, description) VALUES
('Frein', 3600, 150.00, 'Réparation du système de freinage'),
('Vidange', 1800, 80.00, 'Vidange d''huile et remplacement du filtre'),
('Filtre', 1200, 30.00, 'Remplacement des filtres'),
('Batterie', 2400, 120.00, 'Remplacement de la batterie'),
('Amortisseurs', 4800, 300.00, 'Remplacement des amortisseurs'),
('Embrayage', 7200, 450.00, 'Réparation du système d''embrayage'),
('Pneus', 3600, 200.00, 'Remplacement des pneus'),
('Système de refroidissement', 4200, 250.00, 'Réparation du système de refroidissement');

-- Table des interventions signalées
CREATE TABLE interventions (
    intervention_id SERIAL PRIMARY KEY,
    voiture_id INT NOT NULL,
    type_id INT NOT NULL,
    description_panne TEXT,
    priorite VARCHAR(10) DEFAULT 'moyen' 
        CHECK (priorite IN ('faible', 'moyen', 'eleve')),
    date_signalement TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (voiture_id) REFERENCES voitures(voiture_id) ON DELETE CASCADE,
    FOREIGN KEY (type_id) REFERENCES types_intervention(type_id)
);

-- Table des réparations en cours
CREATE TABLE reparations_en_cours (
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
    date_creation TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    date_modification TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (voiture_id) REFERENCES voitures(voiture_id) ON DELETE CASCADE,
    FOREIGN KEY (type_id) REFERENCES types_intervention(type_id),
    FOREIGN KEY (technicien_id) REFERENCES users(user_id)
);

-- Table des commandes
CREATE TABLE commandes (
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
    FOREIGN KEY (user_id) REFERENCES users(user_id),
    FOREIGN KEY (voiture_id) REFERENCES voitures(voiture_id)
);

-- Table des détails de commande
CREATE TABLE commande_details (
    detail_id SERIAL PRIMARY KEY,
    commande_id INT NOT NULL,
    reparation_id INT NOT NULL,
    type_id INT NOT NULL,
    quantite INT DEFAULT 1,
    prix_unitaire DECIMAL(10,2),
    sous_total DECIMAL(10,2),
    FOREIGN KEY (commande_id) REFERENCES commandes(commande_id) ON DELETE CASCADE,
    FOREIGN KEY (reparation_id) REFERENCES reparations_en_cours(reparation_id),
    FOREIGN KEY (type_id) REFERENCES types_intervention(type_id)
);

-- Table des notifications
CREATE TABLE notifications (
    notification_id SERIAL PRIMARY KEY,
    user_id INT NOT NULL,
    titre VARCHAR(255) NOT NULL,
    message TEXT NOT NULL,
    type VARCHAR(50) NOT NULL 
        CHECK (type IN ('reparation_prete', 'paiement', 'mise_a_jour')),
    lue BOOLEAN DEFAULT FALSE,
    date_envoi TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    firebase_token TEXT,
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE
);

-- Index pour améliorer les performances
CREATE INDEX idx_voitures_user_id ON voitures(user_id);
CREATE INDEX idx_reparations_voiture_id ON reparations_en_cours(voiture_id);
CREATE INDEX idx_reparations_statut ON reparations_en_cours(statut);
CREATE INDEX idx_interventions_voiture_id ON interventions(voiture_id);
CREATE INDEX idx_notifications_user_id ON notifications(user_id);
CREATE INDEX idx_notifications_lue ON notifications(lue);
CREATE INDEX idx_commandes_user_id ON commandes(user_id);
CREATE INDEX idx_commandes_statut ON commandes(statut_paiement);

-- Vue pour les statistiques (utile pour le dashboard)
CREATE VIEW vue_statistiques_garage AS
SELECT 
    COUNT(DISTINCT v.user_id) as total_clients,
    COUNT(DISTINCT v.voiture_id) as total_voitures,
    COUNT(DISTINCT CASE WHEN v.statut = 'en_reparation' THEN v.voiture_id END) as voitures_en_reparation,
    COUNT(DISTINCT CASE WHEN v.statut = 'prete' THEN v.voiture_id END) as voitures_pretees,
    COUNT(DISTINCT r.reparation_id) as total_reparations,
    COUNT(DISTINCT CASE WHEN r.statut = 'en_cours' THEN r.reparation_id END) as reparations_en_cours,
    COALESCE(SUM(c.montant_total), 0) as chiffre_affaires_total,
    COALESCE(SUM(CASE WHEN c.statut_paiement = 'paye' THEN c.montant_total END), 0) as chiffre_affaires_paye
FROM voitures v
LEFT JOIN reparations_en_cours r ON v.voiture_id = r.voiture_id
LEFT JOIN commandes c ON v.voiture_id = c.voiture_id;

-- Fonction pour calculer la durée restante d'une réparation
CREATE OR REPLACE FUNCTION calculer_duree_restante(p_reparation_id INT)
RETURNS INTEGER AS $$
DECLARE
    v_duree_totale INT;
    v_progression INT;
    v_duree_restante INT;
BEGIN
    SELECT ti.duree_secondes, r.progression 
    INTO v_duree_totale, v_progression
    FROM reparations_en_cours r
    JOIN types_intervention ti ON r.type_id = ti.type_id
    WHERE r.reparation_id = p_reparation_id;
    
    IF v_duree_totale IS NULL THEN
        RETURN NULL;
    END IF;
    
    v_duree_restante := v_duree_totale * (100 - v_progression) / 100;
    RETURN v_duree_restante;
END;
$$ LANGUAGE plpgsql;

-- Trigger pour mettre à jour la date de modification
CREATE OR REPLACE FUNCTION update_modification_timestamp()
RETURNS TRIGGER AS $$
BEGIN
    NEW.date_modification = CURRENT_TIMESTAMP;
    RETURN NEW;
END;
$$ LANGUAGE plpgsql;

CREATE TRIGGER trigger_update_reparation_timestamp
BEFORE UPDATE ON reparations_en_cours
FOR EACH ROW
EXECUTE FUNCTION update_modification_timestamp();