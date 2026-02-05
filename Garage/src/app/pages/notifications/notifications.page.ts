import { Component, OnInit } from '@angular/core';
import { Router } from '@angular/router';
import { AlertController, LoadingController } from '@ionic/angular';
import { NotificationService } from 'src/app/core/services/notification.service';
import { Notification } from 'src/app/core/models/notification.model';

@Component({
  selector: 'app-notifications',
  templateUrl: './notifications.page.html',
  styleUrls: ['./notifications.page.scss'],
})
export class NotificationsPage implements OnInit {
  notifications: Notification[] = [];
  selectedFilter = 'all';

  constructor(
    private notificationService: NotificationService,
    private router: Router,
    private loadingController: LoadingController,
    private alertController: AlertController
  ) {}

  ngOnInit() {
    this.loadNotifications();
  }

  ionViewWillEnter() {
    this.loadNotifications();
  }

  async loadNotifications() {
    const loading = await this.loadingController.create({
      message: 'Chargement...'
    });
    await loading.present();

    this.notificationService.getMesNotifications().subscribe({
      next: (notifications) => {
        this.notifications = notifications.sort((a, b) => 
          new Date(b.date_envoi).getTime() - new Date(a.date_envoi).getTime()
        );
        loading.dismiss();
      },
      error: async (error) => {
        loading.dismiss();
        const alert = await this.alertController.create({
          header: 'Erreur',
          message: 'Impossible de charger les notifications',
          buttons: ['OK']
        });
        await alert.present();
      }
    });
  }

  get filteredNotifications(): Notification[] {
    if (this.selectedFilter === 'non_lues') {
      return this.notifications.filter(n => !n.lue);
    }
    return this.notifications;
  }

  get nombreNonLues(): number {
    return this.notifications.filter(n => !n.lue).length;
  }

  filterChanged(event: any) {
    this.selectedFilter = event.detail.value;
  }

  getTypeIcon(type: string): string {
    switch (type) {
      case 'reparation_prete': return 'checkmark-circle';
      case 'paiement': return 'card';
      case 'mise_a_jour': return 'information-circle';
      default: return 'notifications';
    }
  }

  getTypeColor(type: string): string {
    switch (type) {
      case 'reparation_prete': return 'success';
      case 'paiement': return 'warning';
      case 'mise_a_jour': return 'primary';
      default: return 'medium';
    }
  }

  formatDate(date: string): string {
    const notifDate = new Date(date);
    const now = new Date();
    const diff = now.getTime() - notifDate.getTime();
    const minutes = Math.floor(diff / 60000);
    const hours = Math.floor(diff / 3600000);
    const days = Math.floor(diff / 86400000);

    if (minutes < 60) {
      return `Il y a ${minutes} min`;
    } else if (hours < 24) {
      return `Il y a ${hours}h`;
    } else if (days === 1) {
      return 'Hier';
    } else if (days < 7) {
      return `Il y a ${days} jours`;
    } else {
      return notifDate.toLocaleDateString('fr-FR');
    }
  }

  async marquerCommeLue(notification: Notification) {
    if (notification.lue) return;

    this.notificationService.marquerCommeLue(notification.notification_id).subscribe({
      next: () => {
        notification.lue = true;
      },
      error: (error) => {
        console.error('Erreur marquer comme lue:', error);
      }
    });
  }

  async marquerToutCommeLu() {
    const loading = await this.loadingController.create({
      message: 'Traitement...'
    });
    await loading.present();

    this.notificationService.marquerToutCommeLu().subscribe({
      next: () => {
        this.notifications.forEach(n => n.lue = true);
        loading.dismiss();
      },
      error: async (error) => {
        loading.dismiss();
        const alert = await this.alertController.create({
          header: 'Erreur',
          message: 'Impossible de marquer toutes les notifications',
          buttons: ['OK']
        });
        await alert.present();
      }
    });
  }

  async supprimerNotification(notification: Notification, event: Event) {
    event.stopPropagation();

    const alert = await this.alertController.create({
      header: 'Supprimer',
      message: 'Voulez-vous supprimer cette notification ?',
      buttons: [
        {
          text: 'Annuler',
          role: 'cancel'
        },
        {
          text: 'Supprimer',
          role: 'destructive',
          handler: () => {
            this.confirmerSuppression(notification.notification_id);
          }
        }
      ]
    });

    await alert.present();
  }

  async confirmerSuppression(notificationId: number) {
    this.notificationService.supprimerNotification(notificationId).subscribe({
      next: () => {
        this.notifications = this.notifications.filter(n => n.notification_id !== notificationId);
      },
      error: async (error) => {
        const alert = await this.alertController.create({
          header: 'Erreur',
          message: 'Impossible de supprimer la notification',
          buttons: ['OK']
        });
        await alert.present();
      }
    });
  }

  doRefresh(event: any) {
    this.notificationService.getMesNotifications().subscribe({
      next: (notifications) => {
        this.notifications = notifications.sort((a, b) => 
          new Date(b.date_envoi).getTime() - new Date(a.date_envoi).getTime()
        );
        event.target.complete();
      },
      error: () => {
        event.target.complete();
      }
    });
  }
}