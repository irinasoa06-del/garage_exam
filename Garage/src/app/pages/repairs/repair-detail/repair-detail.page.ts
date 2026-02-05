import { Component, OnInit } from '@angular/core';
import { ActivatedRoute, Router } from '@angular/router';
import { AlertController, LoadingController } from '@ionic/angular';
import { ReparationService } from 'src/app/core/services/reparation.service';
import { ReparationWithDetails } from 'src/app/core/models/reparation.model';

@Component({
  selector: 'app-repair-detail',
  templateUrl: './repair-detail.page.html',
  styleUrls: ['./repair-detail.page.scss'],
})
export class RepairDetailPage implements OnInit {
  reparationId!: number;
  reparation?: ReparationWithDetails;

  constructor(
    private route: ActivatedRoute,
    private router: Router,
    private reparationService: ReparationService,
    private loadingController: LoadingController,
    private alertController: AlertController
  ) {}

  ngOnInit() {
    this.reparationId = parseInt(this.route.snapshot.paramMap.get('id') || '0');
    if (this.reparationId) {
      this.loadReparation();
    }
  }

  async loadReparation() {
    const loading = await this.loadingController.create({
      message: 'Chargement...'
    });
    await loading.present();

    this.reparationService.getReparationById(this.reparationId).subscribe({
      next: (reparation) => {
        this.reparation = reparation;
        loading.dismiss();
      },
      error: async (error) => {
        loading.dismiss();
        const alert = await this.alertController.create({
          header: 'Erreur',
          message: 'Impossible de charger les détails de la réparation',
          buttons: ['OK']
        });
        await alert.present();
        this.router.navigate(['/repairs']);
      }
    });
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

  formatDate(date: string): string {
    return new Date(date).toLocaleString('fr-FR');
  }

  voirVoiture() {
    if (this.reparation?.voiture_id) {
      this.router.navigate(['/cars', this.reparation.voiture_id]);
    }
  }

  doRefresh(event: any) {
    this.reparationService.getReparationById(this.reparationId).subscribe({
      next: (reparation) => {
        this.reparation = reparation;
        event.target.complete();
      },
      error: () => {
        event.target.complete();
      }
    });
  }
}