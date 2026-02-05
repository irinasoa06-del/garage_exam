import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { BehaviorSubject, Observable } from 'rxjs';
import { tap } from 'rxjs/operators';
import { Storage } from '@ionic/storage-angular';

@Injectable({
  providedIn: 'root'
})
export class AuthService {
  private authState = new BehaviorSubject<boolean>(false);
  private apiUrl = 'http://localhost:8000/api'; // URL de votre backend Laravel

  constructor(
    private http: HttpClient,
    private storage: Storage
  ) {
    this.initStorage();
  }

  private async initStorage() {
    await this.storage.create();
    const token = await this.storage.get('auth_token');
    if (token) {
      this.authState.next(true);
    }
  }

  login(email: string, password: string): Observable<any> {
    return this.http.post(`${this.apiUrl}/login`, { email, password }).pipe(
      tap(async (response: any) => {
        if (response.token) {
          await this.storage.set('auth_token', response.token);
          await this.storage.set('user', response.user);
          this.authState.next(true);
        }
      })
    );
  }

  register(userData: any): Observable<any> {
    return this.http.post(`${this.apiUrl}/register`, userData);
  }

  async logout() {
    await this.storage.remove('auth_token');
    await this.storage.remove('user');
    this.authState.next(false);
  }

  isLoggedIn(): boolean {
    return this.authState.value;
  }

  async getToken(): Promise<string> {
    return await this.storage.get('auth_token');
  }

  async getUser(): Promise<any> {
    return await this.storage.get('user');
  }

  saveUserData(response: any) {
    this.storage.set('auth_token', response.token);
    this.storage.set('user', response.user);
    this.authState.next(true);
  }
}