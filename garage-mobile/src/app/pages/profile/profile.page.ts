import { Component, OnInit } from '@angular/core';
import { Router } from '@angular/router';
import { AlertController, LoadingController } from '@ionic/angular';
import { AuthService } from 'src/app/core/services/auth.service';
import { User } from 'src/app/core/models/user.model';

@Component({
  selector: 'app-profile',
  templateUrl: './profile.page.html',
  styleUrls: ['./profile.page.scss'],
})
export class ProfilePage implements OnInit {
  user?: User;

  constructor(
    private authService: AuthService,
    private router: Router,
    private alertController: AlertController,
    private loadingController: LoadingController
  ) {}

  async ngOnInit() {
    await this.loadUserData();
  }

  async loadUserData() {
    this.user = await this.authService.getUser();
  }

  async modifierProfil() {
    const alert = await this.alertController.create({
      header: 'Modifier le profil',
      message: 'Fonctionnalité à venir...',
      buttons: ['OK']
    });
    await alert.present();
  }

  async voirHistorique() {
    this.router.navigate(['/repairs']);
  }

  async voirVoitures() {
    this.router.navigate(['/cars']);
  }

  async deconnexion() {
    const alert = await this.alertController.create({
      header: 'Déconnexion',
      message: 'Voulez-vous vraiment vous déconnecter ?',
      buttons: [
        {
          text: 'Annuler',
          role: 'cancel'
        },
        {
          text: 'Déconnexion',
          role: 'destructive',
          handler: async () => {
            const loading = await this.loadingController.create({
              message: 'Déconnexion...'
            });
            await loading.present();

            await this.authService.logout();
            await loading.dismiss();
            
            this.router.navigate(['/login']);
          }
        }
      ]
    });

    await alert.present();
  }

  async aPropos() {
    const alert = await this.alertController.create({
      header: 'À propos',
      message: `
        <strong>Garage Mobile</strong><br>
        Version 1.0.0<br><br>
        Application de gestion de réparations automobile.<br><br>
        © 2026 Mon Garage
      `,
      buttons: ['OK']
    });
    await alert.present();
  }

  async aide() {
    const alert = await this.alertController.create({
      header: 'Aide',
      message: `
        <strong>Comment utiliser l'application ?</strong><br><br>
        1. Ajoutez votre voiture<br>
        2. Décrivez les pannes<br>
        3. Suivez les réparations<br>
        4. Payez quand c'est prêt<br><br>
        Pour toute question, contactez le support.
      `,
      buttons: ['OK']
    });
    await alert.present();
  }
}