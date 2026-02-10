import { Component, OnInit } from '@angular/core';
import { FormBuilder, FormGroup, Validators } from '@angular/forms';
import { Router } from '@angular/router';
import { AlertController, LoadingController } from '@ionic/angular';
import { AuthService } from 'src/app/core/services/auth.service';

@Component({
  selector: 'app-login',
  templateUrl: './login.page.html',
  styleUrls: ['./login.page.scss'],
})
export class LoginPage implements OnInit {
  loginForm: FormGroup;
  isSubmitted = false;

  constructor(
    private formBuilder: FormBuilder,
    private authService: AuthService,
    private router: Router,
    private loadingController: LoadingController,
    private alertController: AlertController
  ) {
    this.loginForm = this.formBuilder.group({
      email: ['', [Validators.required, Validators.email]],
      password: ['', [Validators.required, Validators.minLength(6)]]
    });
  }

  ngOnInit() {
    // Vérifier si l'utilisateur est déjà connecté
    if (this.authService.isLoggedIn()) {
      this.router.navigate(['/cars']);
    }
  }

  get errorControl() {
    return this.loginForm.controls;
  }

  async login() {
    this.isSubmitted = true;

    if (this.loginForm.invalid) {
      return;
    }

    const loading = await this.loadingController.create({
      message: 'Connexion en cours...'
    });
    await loading.present();

    this.authService.login(
      this.loginForm.value.email,
      this.loginForm.value.password
    ).subscribe({
      next: async (response) => {
        await loading.dismiss();

        // Stocker le token et les infos utilisateur
        this.authService.saveUserData(response);

        // Redirection vers la liste des voitures
        this.router.navigate(['/cars']);
      },
      error: async (error) => {
        console.error('❌ Login error:', error);
        console.log('❌ Error status:', error.status);
        console.log('❌ Error message:', error.message);
        console.log('❌ Error error body:', error.error);

        await loading.dismiss();

        const alert = await this.alertController.create({
          header: 'Échec de connexion',
          message: error.error?.message || 'Email ou mot de passe incorrect',
          buttons: ['OK']
        });
        await alert.present();
      }
    });
  }

  goToRegister() {
    this.router.navigate(['/register']);
  }

  forgotPassword() {
    // Implémenter la récupération de mot de passe
    console.log('Mot de passe oublié');
  }
}