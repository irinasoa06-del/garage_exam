import { Component, OnInit } from '@angular/core';
import { Router } from '@angular/router';
import { AlertController, LoadingController } from '@ionic/angular';
import { VoitureService } from 'src/app/core/services/voiture.service';
import { Voiture } from 'src/app/core/models/voiture.model';

@Component({
  selector: 'app-car-list',
  templateUrl: './car-list.page.html',
  styleUrls: ['./car-list.page.scss'],
})
export class CarListPage implements OnInit {
  voitures: Voiture[] = [];
  isLoading = false;

  // Statistiques
  statsEnAttente = 0;
  statsEnReparation = 0;
  statsPrete = 0;

  constructor(
    private voitureService: VoitureService,
    private router: Router,
    private loadingController: LoadingController,
    private alertController: AlertController
  ) {}

  ngOnInit() {
    this.loadVoitures();
  }

  ionViewWillEnter() {
    // Recharger à chaque fois qu'on revient sur la page
    this.loadVoitures();
  }

  async loadVoitures() {
    const loading = await this.loadingController.create({
      message: 'Chargement...'
    });
    await loading.present();

    this.voitureService.getMesVoitures().subscribe({
      next: (voitures) => {
        this.voitures = voitures;
        this.calculerStats();
        loading.dismiss();
      },
      error: async (error) => {
        loading.dismiss();
        const alert = await this.alertController.create({
          header: 'Erreur',
          message: 'Impossible de charger vos voitures',
          buttons: ['OK']
        });
        await alert.present();
      }
    });
  }

  calculerStats() {
    this.statsEnAttente = this.voitures.filter(v => v.statut === 'en_attente').length;
    this.statsEnReparation = this.voitures.filter(v => v.statut === 'en_reparation').length;
    this.statsPrete = this.voitures.filter(v => v.statut === 'prete').length;
  }

  getStatutColor(statut: string): string {
    switch (statut) {
      case 'en_attente': return 'warning';
      case 'en_reparation': return 'primary';
      case 'prete': return 'success';
      case 'recuperee': return 'medium';
      default: return 'medium';
    }
  }

  getStatutLabel(statut: string): string {
    switch (statut) {
      case 'en_attente': return 'En attente';
      case 'en_reparation': return 'En réparation';
      case 'prete': return 'Prête';
      case 'recuperee': return 'Récupérée';
      default: return statut;
    }
  }

  ajouterVoiture() {
    this.router.navigate(['/cars/add']);
  }

  voirDetails(voiture: Voiture) {
    this.router.navigate(['/cars', voiture.voiture_id]);
  }

  async supprimerVoiture(voiture: Voiture, event: Event) {
    event.stopPropagation(); // Empêcher la navigation vers les détails

    const alert = await this.alertController.create({
      header: 'Confirmer la suppression',
      message: `Voulez-vous vraiment supprimer ${voiture.marque} ${voiture.modele} ?`,
      buttons: [
        {
          text: 'Annuler',
          role: 'cancel'
        },
        {
          text: 'Supprimer',
          role: 'destructive',
          handler: () => {
            this.confirmerSuppression(voiture.voiture_id);
          }
        }
      ]
    });

    await alert.present();
  }

  async confirmerSuppression(voitureId: number) {
    const loading = await this.loadingController.create({
      message: 'Suppression...'
    });
    await loading.present();

    this.voitureService.deleteVoiture(voitureId).subscribe({
      next: () => {
        loading.dismiss();
        this.loadVoitures(); // Recharger la liste
      },
      error: async (error) => {
        loading.dismiss();
        const alert = await this.alertController.create({
          header: 'Erreur',
          message: 'Impossible de supprimer la voiture',
          buttons: ['OK']
        });
        await alert.present();
      }
    });
  }

  doRefresh(event: any) {
    this.voitureService.getMesVoitures().subscribe({
      next: (voitures) => {
        this.voitures = voitures;
        this.calculerStats();
        event.target.complete();
      },
      error: () => {
        event.target.complete();
      }
    });
  }

  goToProfile() {
    this.router.navigate(['/profile']);
  }

  goToNotifications() {
    this.router.navigate(['/notifications']);
  }
}