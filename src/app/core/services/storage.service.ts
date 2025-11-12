// core/services/storage.service.ts

import { Injectable } from '@angular/core';

@Injectable({
  providedIn: 'root'
})
export class StorageService {
  private readonly TOKEN_KEY = 'agrocean_token';
  private readonly USER_KEY = 'agrocean_user';

  constructor() {}

  // Token methods
  saveToken(token: string): void {
    localStorage.setItem(this.TOKEN_KEY, token);
  }

  getToken(): string | null {
    return localStorage.getItem(this.TOKEN_KEY);
  }

  removeToken(): void {
    localStorage.removeItem(this.TOKEN_KEY);
  }

  // User methods
  saveUser(user: any): void {
    localStorage.setItem(this.USER_KEY, JSON.stringify(user));
  }

  getUser(): any {
    const userStr = localStorage.getItem(this.USER_KEY);
    return userStr ? JSON.parse(userStr) : null;
  }

  removeUser(): void {
    localStorage.removeItem(this.USER_KEY);
  }

  // Clear all
  clear(): void {
    this.removeToken();
    this.removeUser();
  }

  // Check if logged in
  isLoggedIn(): boolean {
    return !!this.getToken();
  }
}
