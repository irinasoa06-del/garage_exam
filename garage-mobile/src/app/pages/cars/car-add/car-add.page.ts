import { Component, OnInit } from '@angular/core';
import { FormBuilder, FormGroup, Validators } from '@angular/forms';
import { Router } from '@angular/router';
import { AlertController, LoadingController } from '@ionic/angular';
import { VoitureService } from 'src/app/core/services/voiture.service';
import { ReparationService } from 'src/app/core/services/reparation.service';
import { TypeIntervention } from 'src/app/core/models/type-intervention.model';
import { CreateVoitureData } from 'src/app/core/models/voiture.model';

@Component({
  selector: 'app-car-add',
  templateUrl: './car-add.page.html',
  styleUrls: ['./car-add.page.scss'],
})
export class CarAddPage implements OnInit {
  carForm: FormGroup;
  typesIntervention: TypeIntervention[] = [];
  selectedInterventions: { type: TypeIntervention, description: string, priorite: string }[] = [];

  constructor(
    private formBuilder: FormBuilder,
    private voitureService: VoitureService,
    private reparationService: ReparationService,
    private router: Router,
    private loadingController: LoadingController,
    private alertController: AlertController
  ) {
    this.carForm = this.formBuilder.group({
      immatriculation: ['', [Validators.required, Validators.pattern(/^[A-Z0-9-]+$/)]],
      marque: ['', [Validators.required, Validators.minLength(2)]],
      modele: ['', [Validators.required, Validators.minLength(2)]],
      annee: ['', [Validators.min(1900), Validators.max(new Date().getFullYear())]],
      couleur: ['']
    });
  }

  ngOnInit() {
    this.loadTypesIntervention();
  }

  async loadTypesIntervention() {
    const loading = await this.loadingController.create({
      message: 'Chargement des types de réparation...'
    });
    await loading.present();

    this.reparationService.getTypesIntervention().subscribe({
      next: (types) => {
        this.typesIntervention = types;
        loading.dismiss();
      },
      error: async (error) => {
        loading.dismiss();
        const alert = await this.alertController.create({
          header: 'Erreur',
          message: 'Impossible de charger les types de réparation',
          buttons: ['OK']
        });
        await alert.present();
      }
    });
  }

  async ajouterIntervention() {
    const alert = await this.alertController.create({
      header: 'Ajouter une panne',
      inputs: [
        {
          name: 'type_id',
          type: 'number',
          placeholder: 'Sélectionner le type',
          min: 1,
          max: this.typesIntervention.length
        },
        {
          name: 'description',
          type: 'textarea',
          placeholder: 'Décrire la panne...'
        },
        {
          name: 'priorite',
          type: 'text',
          value: 'moyen',
          placeholder: 'Priorité (faible/moyen/eleve)'
        }
      ],
      buttons: [
        {
          text: 'Annuler',
          role: 'cancel'
        },
        {
          text: 'Ajouter',
          handler: (data) => {
            if (data.type_id && data.description) {
              const type = this.typesIntervention.find(t => t.type_id === parseInt(data.type_id));
              if (type) {
                this.selectedInterventions.push({
                  type: type,
                  description: data.description,
                  priorite: data.priorite || 'moyen'
                });
              }
            }
          }
        }
      ]
    });

    let message = 'Types disponibles:\n';
    this.typesIntervention.forEach(type => {
      message += `${type.type_id}. ${type.nom} (${type.prix_unitaire} Ar)\n`;
    });
    alert.message = message;

    await alert.present();
  }

  supprimerIntervention(index: number) {
    this.selectedInterventions.splice(index, 1);
  }

  getPrioriteColor(priorite: string): string {
    switch (priorite) {
      case 'faible': return 'success';
      case 'moyen': return 'warning';
      case 'eleve': return 'danger';
      default: return 'medium';
    }
  }

  getTotalEstime(): number {
    return this.selectedInterventions.reduce((sum, si) => sum + si.type.prix_unitaire, 0);
  }

  async soumettre() {
    if (this.carForm.invalid) {
      const alert = await this.alertController.create({
        header: 'Formulaire invalide',
        message: 'Veuillez remplir tous les champs obligatoires',
        buttons: ['OK']
      });
      await alert.present();
      return;
    }

    if (this.selectedInterventions.length === 0) {
      const alert = await this.alertController.create({
        header: 'Aucune panne',
        message: 'Veuillez ajouter au moins une panne à réparer',
        buttons: ['OK']
      });
      await alert.present();
      return;
    }

    const loading = await this.loadingController.create({
      message: 'Enregistrement...'
    });
    await loading.present();

    const voitureData: CreateVoitureData = {
      ...this.carForm.value,
      interventions: this.selectedInterventions.map(si => ({
        type_id: si.type.type_id,
        description_panne: si.description,
        priorite: si.priorite as 'faible' | 'moyen' | 'eleve'
      }))
    };

    this.voitureService.ajouterVoiture(voitureData).subscribe({
      next: async (voiture) => {
        await loading.dismiss();
        
        const alert = await this.alertController.create({
          header: 'Succès',
          message: 'Votre voiture a été ajoutée avec succès',
          buttons: ['OK']
        });
        await alert.present();
        
        this.router.navigate(['/cars']);
      },
      error: async (error) => {
        await loading.dismiss();
        
        const alert = await this.alertController.create({
          header: 'Erreur',
          message: error.error?.message || 'Impossible d\'ajouter la voiture',
          buttons: ['OK']
        });
        await alert.present();
      }
    });
  }

  annuler() {
    this.router.navigate(['/cars']);
  }
}