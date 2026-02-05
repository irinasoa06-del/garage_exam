import { Injectable } from '@angular/core';
import { Observable } from 'rxjs';
import { ApiService } from './api.service';
import { Notification } from '../models/notification.model';

@Injectable({
  providedIn: 'root'
})
export class NotificationService {
  
  constructor(private api: ApiService) {}

  // Récupérer toutes les notifications de l'utilisateur
  getMesNotifications(): Observable<Notification[]> {
    return this.api.get<Notification[]>('notifications');
  }

  // Récupérer les notifications non lues
  getNotificationsNonLues(): Observable<Notification[]> {
    return this.api.get<Notification[]>('notifications', { lue: false });
  }

  // Marquer une notification comme lue
  marquerCommeLue(notificationId: number): Observable<Notification> {
    return this.api.patch<Notification>(`notifications/${notificationId}`, { lue: true });
  }

  // Marquer toutes les notifications comme lues
  marquerToutCommeLu(): Observable<any> {
    return this.api.post<any>('notifications/mark-all-read', {});
  }

  // Enregistrer le token FCM
  enregistrerTokenFCM(token: string): Observable<any> {
    return this.api.post<any>('notifications/register-token', { fcm_token: token });
  }

  // Supprimer une notification
  supprimerNotification(notificationId: number): Observable<any> {
    return this.api.delete(`notifications/${notificationId}`);
  }
}