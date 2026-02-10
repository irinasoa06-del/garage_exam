export interface TypeIntervention {
  type_id: number;
  nom: string;
  duree_secondes: number;
  prix_unitaire: number;
  description?: string;
}

// Les 8 types fixes du sujet
export const TYPES_REPARATION = [
  'Frein',
  'Vidange', 
  'Filtre',
  'Batterie',
  'Amortisseurs',
  'Embrayage',
  'Pneus',
  'Système de refroidissement'
] as const;