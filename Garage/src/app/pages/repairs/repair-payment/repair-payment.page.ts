import { Component, OnInit } from '@angular/core';
import { ActivatedRoute, Router } from '@angular/router';
import { FormBuilder, FormGroup, Validators } from '@angular/forms';
import { AlertController, LoadingController } from '@ionic/angular';
import { VoitureService } from 'src/app/core/services/voiture.service';
import { ReparationService } from 'src/app/core/services/reparation.service';
import { PaiementService } from 'src/app/core/services/paiement.service';
import { Voiture } from 'src/app/core/models/voiture.model';
import { ReparationWithDetails } from 'src/app/core/models/reparation.model';

@Component({
  selector: 'app-repair-payment',
  templateUrl: './repair-payment.page.html',
  styleUrls: ['./repair-payment.page.scss'],
})
export class RepairPaymentPage implements OnInit {
  voitureId!: number;
  voiture?: Voiture;
  reparations: ReparationWithDetails[] = [];
  montantTotal = 0;
  paymentForm: FormGroup;

  modesPaiement = [
    { value: 'especes', label: 'Espèces' },
    { value: 'carte', label: 'Carte bancaire' },
    { value: 'mobile_money', label: 'Mobile Money' },
    { value: 'virement', label: 'Virement bancaire' }
  ];

  constructor(
    private route: ActivatedRoute,
    private router: Router,
    private formBuilder: FormBuilder,
    private voitureService: VoitureService,
    private reparationService: ReparationService,
    private paiementService: PaiementService,
    private loadingController: LoadingController,
    private alertController: AlertController
  ) {
    this.paymentForm = this.formBuilder.group({
      mode_paiement: ['especes', Validators.required]
    });
  }

  ngOnInit() {
    this.voitureId = parseInt(this.route.snapshot.paramMap.get('id') || '0');
    if (this.voitureId) {
      this.loadData();
    }
  }

  async loadData() {
    const loading = await this.loadingController.create({
      message: 'Chargement...'
    });
    await loading.present();

    // Charger voiture
    this.voitureService.getVoitureById(this.voitureId).subscribe({
      next: (voiture) => {
        this.voiture = voiture;
      },
      error: (error) => {
        console.error('Erreur chargement voiture:', error);
      }
    });

    // Charger réparations
    this.reparationService.getReparationsByVoiture(this.voitureId).subscribe({
      next: (reparations) => {
        // Filtrer uniquement les réparations terminées
        this.reparations = reparations.filter(r => r.statut === 'terminee');
        this.calculerMontantTotal();
        loading.dismiss();

        if (this.reparations.length === 0) {
          this.showErrorAndGoBack('Aucune réparation terminée à payer');
        }
      },
      error: async (error) => {
        loading.dismiss();
        this.showErrorAndGoBack('Impossible de charger les réparations');
      }
    });
  }

  calculerMontantTotal() {
    this.montantTotal = this.reparations.reduce((sum, rep) => {
      return sum + (rep.type_intervention?.prix_unitaire || 0);
    }, 0);
  }

  async showErrorAndGoBack(message: string) {
    const alert = await this.alertController.create({
      header: 'Erreur',
      message: message,
      buttons: [
        {
          text: 'OK',
          handler: () => {
            this.router.navigate(['/cars', this.voitureId]);
          }
        }
      ]
    });
    await alert.present();
  }

  async confirmerPaiement() {
    if (this.paymentForm.invalid) {
      return;
    }

    const alert = await this.alertController.create({
      header: 'Confirmer le paiement',
      message: `Montant à payer: ${this.montantTotal} Ar<br>Mode: ${this.getModePaiementLabel(this.paymentForm.value.mode_paiement)}`,
      buttons: [
        {
          text: 'Annuler',
          role: 'cancel'
        },
        {
          text: 'Confirmer',
          handler: () => {
            this.effectuerPaiement();
          }
        }
      ]
    });

    await alert.present();
  }

  async effectuerPaiement() {
    const loading = await this.loadingController.create({
      message: 'Traitement du paiement...'
    });
    await loading.present();

    const paymentData = {
      voiture_id: this.voitureId,
      mode_paiement: this.paymentForm.value.mode_paiement,
      reparation_ids: this.reparations.map(r => r.reparation_id)
    };

    this.paiementService.effectuerPaiement(paymentData).subscribe({
      next: async (commande) => {
        await loading.dismiss();

        const alert = await this.alertController.create({
          header: 'Paiement réussi !',
          message: `Votre paiement de ${this.montantTotal} Ar a été effectué avec succès.<br><br>Numéro de transaction: ${commande.transaction_id || 'N/A'}`,
          buttons: [
            {
              text: 'OK',
              handler: () => {
                this.router.navigate(['/cars']);
              }
            }
          ]
        });
        await alert.present();
      },
      error: async (error) => {
        await loading.dismiss();

        const alert = await this.alertController.create({
          header: 'Échec du paiement',
          message: error.error?.message || 'Une erreur est survenue lors du paiement',
          buttons: ['OK']
        });
        await alert.present();
      }
    });
  }

  getModePaiementLabel(value: string): string {
    const mode = this.modesPaiement.find(m => m.value === value);
    return mode ? mode.label : value;
  }

  annuler() {
    this.router.navigate(['/cars', this.voitureId]);
  }
}