import { Component, OnInit } from '@angular/core';
import { ActivatedRoute, Router } from '@angular/router';
import { AlertController, LoadingController } from '@ionic/angular';
import { VoitureService } from 'src/app/core/services/voiture.service';
import { ReparationService } from 'src/app/core/services/reparation.service';
import { Voiture } from 'src/app/core/models/voiture.model';
import { ReparationWithDetails } from 'src/app/core/models/reparation.model';

@Component({
  selector: 'app-car-detail',
  templateUrl: './car-detail.page.html',
  styleUrls: ['./car-detail.page.scss'],
})
export class CarDetailPage implements OnInit {
  voitureId!: number;
  voiture?: Voiture;
  reparations: ReparationWithDetails[] = [];
  montantTotal = 0;

  constructor(
    private route: ActivatedRoute,
    private router: Router,
    private voitureService: VoitureService,
    private reparationService: ReparationService,
    private loadingController: LoadingController,
    private alertController: AlertController
  ) {}

  ngOnInit() {
    this.voitureId = parseInt(this.route.snapshot.paramMap.get('id') || '0');
    if (this.voitureId) {
      this.loadVoitureDetails();
      this.loadReparations();
    }
  }

  async loadVoitureDetails() {
    const loading = await this.loadingController.create({
      message: 'Chargement...'
    });
    await loading.present();

    this.voitureService.getVoitureById(this.voitureId).subscribe({
      next: (voiture) => {
        this.voiture = voiture;
        loading.dismiss();
      },
      error: async (error) => {
        loading.dismiss();
        const alert = await this.alertController.create({
          header: 'Erreur',
          message: 'Impossible de charger les détails de la voiture',
          buttons: ['OK']
        });
        await alert.present();
        this.router.navigate(['/cars']);
      }
    });
  }

  async loadReparations() {
    this.reparationService.getReparationsByVoiture(this.voitureId).subscribe({
      next: (reparations) => {
        this.reparations = reparations;
        this.calculerMontantTotal();
      },
      error: (error) => {
        console.error('Erreur chargement réparations:', error);
      }
    });
  }

  calculerMontantTotal() {
    this.montantTotal = this.reparations.reduce((sum, rep) => {
      return sum + (rep.type_intervention?.prix_unitaire || 0);
    }, 0);
  }

  getStatutColor(statut: string): string {
    switch (statut) {
      case 'en_attente': return 'warning';
      case 'en_cours': return 'primary';
      case 'terminee': return 'success';
      default: return 'medium';
    }
  }

  getStatutLabel(statut: string): string {
    switch (statut) {
      case 'en_attente': return 'En attente';
      case 'en_cours': return 'En cours';
      case 'terminee': return 'Terminée';
      default: return statut;
    }
  }

  formatDuree(secondes: number): string {
    const heures = Math.floor(secondes / 3600);
    const minutes = Math.floor((secondes % 3600) / 60);
    
    if (heures > 0) {
      return `${heures}h ${minutes}min`;
    }
    return `${minutes}min`;
  }

  async payerReparations() {
    // Vérifier si toutes les réparations sont terminées
    const allTerminees = this.reparations.every(r => r.statut === 'terminee');
    
    if (!allTerminees) {
      const alert = await this.alertController.create({
        header: 'Réparations non terminées',
        message: 'Toutes les réparations doivent être terminées avant de pouvoir payer',
        buttons: ['OK']
      });
      await alert.present();
      return;
    }

    // Naviguer vers la page de paiement
    this.router.navigate(['/repairs/payment', this.voitureId]);
  }

  async supprimerVoiture() {
    const alert = await this.alertController.create({
      header: 'Confirmer la suppression',
      message: `Voulez-vous vraiment supprimer ${this.voiture?.marque} ${this.voiture?.modele} ?`,
      buttons: [
        {
          text: 'Annuler',
          role: 'cancel'
        },
        {
          text: 'Supprimer',
          role: 'destructive',
          handler: async () => {
            const loading = await this.loadingController.create({
              message: 'Suppression...'
            });
            await loading.present();

            this.voitureService.deleteVoiture(this.voitureId).subscribe({
              next: () => {
                loading.dismiss();
                this.router.navigate(['/cars']);
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
        }
      ]
    });

    await alert.present();
  }

  doRefresh(event: any) {
    this.loadVoitureDetails();
    this.reparationService.getReparationsByVoiture(this.voitureId).subscribe({
      next: (reparations) => {
        this.reparations = reparations;
        this.calculerMontantTotal();
        event.target.complete();
      },
      error: () => {
        event.target.complete();
      }
    });
  }
}