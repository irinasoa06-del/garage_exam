import { Injectable } from '@angular/core';
import { Observable } from 'rxjs';
import { ApiService } from './api.service';
import { Commande, CreatePaymentData } from '../models/commande.model';

@Injectable({
  providedIn: 'root'
})
export class PaiementService {

  constructor(private api: ApiService) {}

  // Effectuer un paiement
  effectuerPaiement(data: CreatePaymentData): Observable<Commande> {
    return this.api.post<Commande>('paiements', data);
  }

  // Récupérer l'historique des paiements
  getMesPaiements(): Observable<Commande[]> {
    return this.api.get<Commande[]>('commandes');
  }

  // Récupérer un paiement par ID
  getPaiementById(id: number): Observable<Commande> {
    return this.api.get<Commande>(`commandes/${id}`);
  }

  // Récupérer les paiements d'une voiture
  getPaiementsByVoiture(voitureId: number): Observable<Commande[]> {
    return this.api.get<Commande[]>(`voitures/${voitureId}/commandes`);
  }
}