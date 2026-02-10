import { Component, OnInit } from '@angular/core';
import { FormBuilder, FormGroup, Validators } from '@angular/forms';
import { Router } from '@angular/router';
import { AlertController, LoadingController } from '@ionic/angular';
import { AuthService } from 'src/app/core/services/auth.service';

@Component({
  selector: 'app-register',
  templateUrl: './register.page.html',
  styleUrls: ['./register.page.scss'],
})
export class RegisterPage implements OnInit {
  registerForm: FormGroup;
  isSubmitted = false;

  constructor(
    private formBuilder: FormBuilder,
    private authService: AuthService,
    private router: Router,
    private loadingController: LoadingController,
    private alertController: AlertController
  ) {
    this.registerForm = this.formBuilder.group({
      nom: ['', [Validators.required, Validators.minLength(2)]],
      prenom: ['', [Validators.required, Validators.minLength(2)]],
      email: ['', [Validators.required, Validators.email]],
      telephone: ['', [Validators.pattern(/^[0-9]{10}$/)]],
      mot_de_passe: ['', [Validators.required, Validators.minLength(6)]],
      confirmer_mot_de_passe: ['', [Validators.required]]
    }, {
      validators: this.passwordMatchValidator
    });
  }

  ngOnInit() {
    // Vérifier si l'utilisateur est déjà connecté
    if (this.authService.isLoggedIn()) {
      this.router.navigate(['/cars']);
    }
  }

  // Validateur personnalisé pour vérifier que les mots de passe correspondent
  passwordMatchValidator(formGroup: FormGroup) {
    const password = formGroup.get('mot_de_passe')?.value;
    const confirmPassword = formGroup.get('confirmer_mot_de_passe')?.value;
    
    if (password !== confirmPassword) {
      formGroup.get('confirmer_mot_de_passe')?.setErrors({ passwordMismatch: true });
    } else {
      formGroup.get('confirmer_mot_de_passe')?.setErrors(null);
    }
  }

  get errorControl() {
    return this.registerForm.controls;
  }

  async register() {
    this.isSubmitted = true;
    
    if (this.registerForm.invalid) {
      return;
    }

    const loading = await this.loadingController.create({
      message: 'Création du compte...'
    });
    await loading.present();

    // Préparer les données (sans la confirmation du mot de passe)
    const { confirmer_mot_de_passe, ...userData } = this.registerForm.value;

    this.authService.register(userData).subscribe({
      next: async (response) => {
        await loading.dismiss();
        
        // Afficher un message de succès
        const alert = await this.alertController.create({
          header: 'Inscription réussie',
          message: 'Votre compte a été créé avec succès. Connectez-vous maintenant.',
          buttons: ['OK']
        });
        await alert.present();
        
        // Rediriger vers la page de login
        this.router.navigate(['/login']);
      },
      error: async (error) => {
        await loading.dismiss();
        
        let errorMessage = 'Une erreur est survenue lors de l\'inscription';
        
        if (error.error?.message) {
          errorMessage = error.error.message;
        } else if (error.error?.errors) {
          // Afficher la première erreur de validation
          const firstError = Object.values(error.error.errors)[0];
          if (Array.isArray(firstError)) {
            errorMessage = firstError[0];
          }
        }
        
        const alert = await this.alertController.create({
          header: 'Échec de l\'inscription',
          message: errorMessage,
          buttons: ['OK']
        });
        await alert.present();
      }
    });
  }

  goToLogin() {
    this.router.navigate(['/login']);
  }
}