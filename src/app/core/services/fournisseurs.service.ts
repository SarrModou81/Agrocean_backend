import { Injectable } from '@angular/core';
import { HttpClient, HttpParams } from '@angular/common/http';
import { Observable } from 'rxjs';
import { environment } from '../../../environments/environment';
import { Fournisseur, PaginatedResponse } from '../models';

@Injectable({
  providedIn: 'root'
})
export class FournisseursService {
  private apiUrl = `${environment.apiUrl}/fournisseurs`;

  constructor(private http: HttpClient) {}

  getAll(params?: any): Observable<PaginatedResponse<Fournisseur>> {
    let httpParams = new HttpParams();
    if (params) {
      Object.keys(params).forEach(key => {
        if (params[key] !== null && params[key] !== undefined) {
          httpParams = httpParams.set(key, params[key]);
        }
      });
    }
    return this.http.get<PaginatedResponse<Fournisseur>>(this.apiUrl, { params: httpParams });
  }

  getById(id: number): Observable<{ success: boolean; data: Fournisseur }> {
    return this.http.get<{ success: boolean; data: Fournisseur }>(`${this.apiUrl}/${id}`);
  }

  create(data: Partial<Fournisseur>): Observable<{ success: boolean; data: Fournisseur; message: string }> {
    return this.http.post<{ success: boolean; data: Fournisseur; message: string }>(this.apiUrl, data);
  }

  update(id: number, data: Partial<Fournisseur>): Observable<{ success: boolean; data: Fournisseur; message: string }> {
    return this.http.put<{ success: boolean; data: Fournisseur; message: string }>(`${this.apiUrl}/${id}`, data);
  }

  delete(id: number): Observable<{ success: boolean; message: string }> {
    return this.http.delete<{ success: boolean; message: string }>(`${this.apiUrl}/${id}`);
  }

  getHistorique(id: number): Observable<any> {
    return this.http.get<any>(`${this.apiUrl}/${id}/historique`);
  }

  getTopFournisseurs(): Observable<any> {
    return this.http.get<any>(`${this.apiUrl}/top/meilleurs`);
  }

  rechercher(params: any): Observable<any> {
    let httpParams = new HttpParams();
    if (params) {
      Object.keys(params).forEach(key => {
        if (params[key] !== null && params[key] !== undefined) {
          httpParams = httpParams.set(key, params[key]);
        }
      });
    }
    return this.http.get<any>(`${this.apiUrl}/recherche/avancee`, { params: httpParams });
  }

  evaluer(id: number, data: any): Observable<{ success: boolean; message: string }> {
    return this.http.post<{ success: boolean; message: string }>(`${this.apiUrl}/${id}/evaluer`, data);
  }
}
