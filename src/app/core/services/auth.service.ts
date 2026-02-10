import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { BehaviorSubject, Observable } from 'rxjs';
import { tap, map } from 'rxjs/operators';
import { Storage } from '@ionic/storage-angular';
import { environment } from 'src/environments/environment';

@Injectable({
  providedIn: 'root'
})
export class AuthService {
  private authState = new BehaviorSubject<boolean>(false);
  private apiUrl = environment.apiUrl;

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
    console.log('🔵 AuthService: Sending login request to:', `${this.apiUrl}/login`);
    // Request as text first to avoid Angular's automatic JSON parsing failure
    return this.http.post(`${this.apiUrl}/login`, { email, password }, { responseType: 'text' }).pipe(
      tap((rawResponse: string) => {
        console.log('🟢 AuthService: Received raw response (length):', rawResponse.length);
        console.log('🟢 AuthService: Raw response start:', rawResponse.substring(0, 50));
      }),
      map((rawResponse: string) => {
        try {
          // Find the first opening brace
          const jsonStartIndex = rawResponse.indexOf('{');
          if (jsonStartIndex === -1) {
            throw new Error('No JSON found in response');
          }
          const cleanJson = rawResponse.substring(jsonStartIndex);
          return JSON.parse(cleanJson);
        } catch (e) {
          console.error('❌ AuthService: JSON Parse Error:', e);
          throw e;
        }
      }),
      tap(async (response: any) => {
        console.log('🟢 AuthService: Parsed response:', response);
        if (response.token) {
          console.log('✅ AuthService: Saving token and user data');
          await this.storage.set('auth_token', response.token);
          await this.storage.set('user', response.user);
          this.authState.next(true);
        } else {
          console.error('❌ AuthService: No token in response!');
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