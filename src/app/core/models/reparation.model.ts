import { TypeIntervention } from './type-intervention.model';
import { Voiture } from './voiture.model';

export interface Reparation {
  reparation_id: number;
  voiture_id: number;
  type_id: number;
  slot_garage?: number;
  date_debut?: string;
  date_fin_estimee?: string;
  statut: 'en_attente' | 'en_cours' | 'terminee';
  progression: number;
  technicien_id?: number;
  slot_attente: boolean;
  date_creation: string;
  date_modification: string;
  
  // Relations (si incluses par le backend)
  type_intervention?: TypeIntervention;
  voiture?: Voiture;
}

export interface ReparationWithDetails extends Reparation {
  type_intervention: TypeIntervention;
  voiture: Voiture;
  duree_restante?: number;
}