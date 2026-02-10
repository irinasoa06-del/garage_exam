import { Injectable } from '@angular/core';
import { CanActivate, Router } from '@angular/router';
import { AuthService } from '../services/auth.service';

@Injectable({
  providedIn: 'root'
})
export class GuestGuard implements CanActivate {

  constructor(
    private authService: AuthService,
    private router: Router
  ) {}

  canActivate(): boolean {
    // Si déjà connecté, rediriger vers la liste des voitures
    if (this.authService.isLoggedIn()) {
      this.router.navigate(['/cars']);
      return false;
    }

    // Sinon, autoriser l'accès
    return true;
  }
}