import { Injectable } from '@angular/core';
import { Observable } from 'rxjs';
import { ApiService } from './api.service';
import { Voiture, CreateVoitureData } from '../models/voiture.model';

@Injectable({
  providedIn: 'root'
})
export class VoitureService {

  constructor(private api: ApiService) {}

  // Récupérer toutes les voitures de l'utilisateur connecté
  getMesVoitures(): Observable<Voiture[]> {
    return this.api.get<Voiture[]>('voitures');
  }

  // Récupérer une voiture par ID
  getVoitureById(id: number): Observable<Voiture> {
    return this.api.get<Voiture>(`voitures/${id}`);
  }

  // Ajouter une nouvelle voiture avec les pannes
  ajouterVoiture(data: CreateVoitureData): Observable<Voiture> {
    return this.api.post<Voiture>('voitures', data);
  }

  // Mettre à jour une voiture
  updateVoiture(id: number, data: Partial<Voiture>): Observable<Voiture> {
    return this.api.put<Voiture>(`voitures/${id}`, data);
  }

  // Supprimer une voiture
  deleteVoiture(id: number): Observable<any> {
    return this.api.delete(`voitures/${id}`);
  }

  // Récupérer les voitures par statut
  getVoituresByStatut(statut: string): Observable<Voiture[]> {
    return this.api.get<Voiture[]>('voitures', { statut });
  }
}