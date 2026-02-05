export interface Voiture {
  voiture_id: number;
  user_id: number;
  immatriculation: string;
  marque: string;
  modele: string;
  annee?: number;
  couleur?: string;
  date_ajout: string;
  statut: 'en_attente' | 'en_reparation' | 'prete' | 'recuperee';
}

export interface CreateVoitureData {
  immatriculation: string;
  marque: string;
  modele: string;
  annee?: number;
  couleur?: string;
  interventions: CreateInterventionData[];
}

export interface CreateInterventionData {
  type_id: number;
  description_panne: string;
  priorite?: 'faible' | 'moyen' | 'eleve';
}