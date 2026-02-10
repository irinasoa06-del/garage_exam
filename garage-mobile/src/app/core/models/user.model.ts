export interface User {
  user_id: number;
  nom: string;
  prenom: string;
  email: string;
  telephone?: string;
  adresse?: string;
  date_inscription: string;
  role: 'client' | 'admin';
  firebase_uid?: string;
}

export interface RegisterData {
  nom: string;
  prenom: string;
  email: string;
  mot_de_passe: string;
  telephone?: string;
  adresse?: string;
}

export interface LoginResponse {
  token: string;
  user: User;
}