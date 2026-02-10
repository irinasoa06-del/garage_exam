import { Component, OnInit } from '@angular/core';
import { Router } from '@angular/router';
import { AlertController, LoadingController } from '@ionic/angular';
import { ReparationService } from 'src/app/core/services/reparation.service';
import { ReparationWithDetails } from 'src/app/core/models/reparation.model';

@Component({
  selector: 'app-repair-list',
  templateUrl: './repair-list.page.html',
  styleUrls: ['./repair-list.page.scss'],
})
export class RepairListPage implements OnInit {
  reparations: ReparationWithDetails[] = [];
  filteredReparations: ReparationWithDetails[] = [];
  selectedFilter = 'all';

  constructor(
    private reparationService: ReparationService,
    private router: Router,
    private loadingController: LoadingController,
    private alertController: AlertController
  ) {}

  ngOnInit() {
    this.loadReparations();
  }

  ionViewWillEnter() {
    this.loadReparations();
  }

  async loadReparations() {
    const loading = await this.loadingController.create({
      message: 'Chargement...'
    });
    await loading.present();

    this.reparationService.getMesReparations().subscribe({
      next: (reparations) => {
        this.reparations = reparations;
        this.applyFilter(this.selectedFilter);
        loading.dismiss();
      },
      error: async (error) => {
        loading.dismiss();
        const alert = await this.alertController.create({
          header: 'Erreur',
          message: 'Impossible de charger les réparations',
          buttons: ['OK']
        });
        await alert.present();
      }
    });
  }

  filterChanged(event: any) {
    this.selectedFilter = event.detail.value;
    this.applyFilter(this.selectedFilter);
  }

  applyFilter(filter: string) {
    if (filter === 'all') {
      this.filteredReparations = this.reparations;
    } else {
      this.filteredReparations = this.reparations.filter(r => r.statut === filter);
    }
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

  voirDetails(reparation: ReparationWithDetails) {
    this.router.navigate(['/repairs', reparation.reparation_id]);
  }

  voirVoiture(voitureId: number, event: Event) {
    event.stopPropagation();
    this.router.navigate(['/cars', voitureId]);
  }

  doRefresh(event: any) {
    this.reparationService.getMesReparations().subscribe({
      next: (reparations) => {
        this.reparations = reparations;
        this.applyFilter(this.selectedFilter);
        event.target.complete();
      },
      error: () => {
        event.target.complete();
      }
    });
  }
}