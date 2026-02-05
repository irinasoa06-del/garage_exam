import { Injectable } from '@angular/core';
import { Observable } from 'rxjs';
import { ApiService } from './api.service';
import { Reparation, ReparationWithDetails } from '../models/reparation.model';
import { TypeIntervention } from '../models/type-intervention.model';

@Injectable({
  providedIn: 'root'
})
export class ReparationService {

  constructor(private api: ApiService) {}

  // Récupérer toutes les réparations de l'utilisateur
  getMesReparations(): Observable<ReparationWithDetails[]> {
    return this.api.get<ReparationWithDetails[]>('reparations');
  }

  // Récupérer les réparations d'une voiture
  getReparationsByVoiture(voitureId: number): Observable<ReparationWithDetails[]> {
    return this.api.get<ReparationWithDetails[]>(`voitures/${voitureId}/reparations`);
  }

  // Récupérer une réparation par ID
  getReparationById(id: number): Observable<ReparationWithDetails> {
    return this.api.get<ReparationWithDetails>(`reparations/${id}`);
  }

  // Récupérer les réparations par statut
  getReparationsByStatut(statut: string): Observable<ReparationWithDetails[]> {
    return this.api.get<ReparationWithDetails[]>('reparations', { statut });
  }

  // Récupérer tous les types d'intervention
  getTypesIntervention(): Observable<TypeIntervention[]> {
    return this.api.get<TypeIntervention[]>('types-intervention');
  }

  // Calculer le montant total des réparations d'une voiture
  calculerMontantTotal(voitureId: number): Observable<{ montant_total: number }> {
    return this.api.get<{ montant_total: number }>(`voitures/${voitureId}/montant-total`);
  }
}