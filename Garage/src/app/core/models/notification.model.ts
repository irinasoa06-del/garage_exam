export interface Notification {
  notification_id: number;
  user_id: number;
  titre: string;
  message: string;
  type: 'reparation_prete' | 'paiement' | 'mise_a_jour';
  lue: boolean;
  date_envoi: string;
  firebase_token?: string;
}

export interface FirebaseMessage {
  notification: {
    title: string;
    body: string;
  };
  data?: {
    type: string;
    voiture_id?: string;
    reparation_id?: string;
  };
}