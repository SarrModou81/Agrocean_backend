// core/services/auth.service.ts

import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { Router } from '@angular/router';
import { Observable, BehaviorSubject, tap } from 'rxjs';
import { jwtDecode } from 'jwt-decode';
import { environment } from '../../../environments/environment';
import { StorageService } from './storage.service';
import {
  User,
  LoginRequest,
  LoginResponse,
  RegisterRequest,
  AuthUser
} from '../models';

@Injectable({
  providedIn: 'root'
})
export class AuthService {
  private apiUrl = environment.apiUrl;
  private currentUserSubject = new BehaviorSubject<User | null>(null);
  public currentUser$ = this.currentUserSubject.asObservable();

  constructor(
    private http: HttpClient,
    private router: Router,
    private storage: StorageService
  ) {
    // Charger l'utilisateur depuis le storage au démarrage
    const user = this.storage.getUser();
    if (user) {
      this.currentUserSubject.next(user);
    }
  }

  /**
   * Connexion
   */
  login(credentials: LoginRequest): Observable<LoginResponse> {
    return this.http.post<LoginResponse>(`${this.apiUrl}/auth/login`, credentials)
      .pipe(
        tap(response => {
          if (response.token) {
            this.storage.saveToken(response.token);
            this.storage.saveUser(response.user);
            this.currentUserSubject.next(response.user);
          }
        })
      );
  }

  /**
   * Inscription
   */
  register(data: RegisterRequest): Observable<any> {
    return this.http.post(`${this.apiUrl}/auth/register`, data);
  }

  /**
   * Déconnexion
   */
  logout(): Observable<any> {
    return this.http.post(`${this.apiUrl}/auth/logout`, {})
      .pipe(
        tap(() => {
          this.clearAuth();
          this.router.navigate(['/auth/login']);
        })
      );
  }

  /**
   * Rafraîchir le token
   */
  refreshToken(): Observable<any> {
    return this.http.post(`${this.apiUrl}/auth/refresh`, {})
      .pipe(
        tap((response: any) => {
          if (response.token) {
            this.storage.saveToken(response.token);
          }
        })
      );
  }

  /**
   * Obtenir l'utilisateur connecté
   */
  me(): Observable<AuthUser> {
    return this.http.get<AuthUser>(`${this.apiUrl}/auth/me`)
      .pipe(
        tap(response => {
          if (response.user) {
            this.storage.saveUser(response.user);
            this.currentUserSubject.next(response.user);
          }
        })
      );
  }

  /**
   * Changer le mot de passe
   */
  changePassword(data: any): Observable<any> {
    return this.http.post(`${this.apiUrl}/auth/change-password`, data);
  }

  /**
   * Mettre à jour le profil
   */
  updateProfile(data: any): Observable<any> {
    return this.http.put(`${this.apiUrl}/auth/profile`, data)
      .pipe(
        tap((response: any) => {
          if (response.user) {
            this.storage.saveUser(response.user);
            this.currentUserSubject.next(response.user);
          }
        })
      );
  }

  /**
   * Vérifier si l'utilisateur est connecté
   */
  isAuthenticated(): boolean {
    const token = this.storage.getToken();
    if (!token) return false;

    try {
      const decoded: any = jwtDecode(token);
      const isExpired = decoded.exp * 1000 < Date.now();
      return !isExpired;
    } catch {
      return false;
    }
  }

  /**
   * Obtenir l'utilisateur actuel
   */
  getCurrentUser(): User | null {
    return this.currentUserSubject.value;
  }

  /**
   * Obtenir le rôle de l'utilisateur
   */
  getUserRole(): string | null {
    const user = this.getCurrentUser();
    return user ? user.role : null;
  }

  /**
   * Vérifier si l'utilisateur a un rôle spécifique
   */
  hasRole(role: string): boolean {
    const userRole = this.getUserRole();
    return userRole === role;
  }

  /**
   * Vérifier si l'utilisateur a l'un des rôles
   */
  hasAnyRole(roles: string[]): boolean {
    const userRole = this.getUserRole();
    return userRole ? roles.includes(userRole) : false;
  }

  /**
   * Nettoyer l'authentification
   */
  private clearAuth(): void {
    this.storage.clear();
    this.currentUserSubject.next(null);
  }
}
