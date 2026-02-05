export interface Commande {
  commande_id: number;
  user_id: number;
  voiture_id: number;
  date_commande: string;
  montant_total: number;
  statut_paiement: 'en_attente' | 'paye' | 'echoue';
  mode_paiement?: string;
  date_paiement?: string;
  transaction_id?: string;
}

export interface CommandeDetail {
  detail_id: number;
  commande_id: number;
  reparation_id: number;
  type_id: number;
  quantite: number;
  prix_unitaire: number;
  sous_total: number;
}

export interface CreatePaymentData {
  voiture_id: number;
  mode_paiement: string;
  reparation_ids: number[];
}