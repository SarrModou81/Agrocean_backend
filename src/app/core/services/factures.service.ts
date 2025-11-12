import { Injectable } from '@angular/core';
import { HttpClient, HttpParams } from '@angular/common/http';
import { Observable } from 'rxjs';
import { environment } from '../../../environments/environment';
import { Facture, FactureFournisseur, PaginatedResponse } from '../models';

@Injectable({
  providedIn: 'root'
})
export class FacturesService {
  private apiUrl = `${environment.apiUrl}/factures`;
  private fournisseursUrl = `${environment.apiUrl}/factures-fournisseurs`;

  constructor(private http: HttpClient) {}

  // ===== FACTURES CLIENTS =====
  getAll(params?: any): Observable<PaginatedResponse<Facture>> {
    let httpParams = new HttpParams();
    if (params) {
      Object.keys(params).forEach(key => {
        if (params[key] !== null && params[key] !== undefined) {
          httpParams = httpParams.set(key, params[key]);
        }
      });
    }
    return this.http.get<PaginatedResponse<Facture>>(this.apiUrl, { params: httpParams });
  }

  getById(id: number): Observable<{ success: boolean; data: Facture }> {
    return this.http.get<{ success: boolean; data: Facture }>(`${this.apiUrl}/${id}`);
  }

  create(data: Partial<Facture>): Observable<{ success: boolean; data: Facture; message: string }> {
    return this.http.post<{ success: boolean; data: Facture; message: string }>(this.apiUrl, data);
  }

  update(id: number, data: Partial<Facture>): Observable<{ success: boolean; data: Facture; message: string }> {
    return this.http.put<{ success: boolean; data: Facture; message: string }>(`${this.apiUrl}/${id}`, data);
  }

  delete(id: number): Observable<{ success: boolean; message: string }> {
    return this.http.delete<{ success: boolean; message: string }>(`${this.apiUrl}/${id}`);
  }

  impayees(): Observable<{ success: boolean; data: Facture[] }> {
    return this.http.get<{ success: boolean; data: Facture[] }>(`${this.apiUrl}/impayees/liste`);
  }

  echues(): Observable<{ success: boolean; data: Facture[] }> {
    return this.http.get<{ success: boolean; data: Facture[] }>(`${this.apiUrl}/echues/liste`);
  }

  genererPDF(id: number): Observable<Blob> {
    return this.http.get(`${this.apiUrl}/${id}/generer-pdf`, { responseType: 'blob' });
  }

  envoyer(id: number, data: any): Observable<{ success: boolean; message: string }> {
    return this.http.post<{ success: boolean; message: string }>(`${this.apiUrl}/${id}/envoyer`, data);
  }

  statistiques(): Observable<any> {
    return this.http.get<any>(`${this.apiUrl}/statistiques/analyse`);
  }

  // ===== FACTURES FOURNISSEURS =====
  getFournisseurs(params?: any): Observable<PaginatedResponse<FactureFournisseur>> {
    let httpParams = new HttpParams();
    if (params) {
      Object.keys(params).forEach(key => {
        if (params[key] !== null && params[key] !== undefined) {
          httpParams = httpParams.set(key, params[key]);
        }
      });
    }
    return this.http.get<PaginatedResponse<FactureFournisseur>>(this.fournisseursUrl, { params: httpParams });
  }

  getFournisseurById(id: number): Observable<{ success: boolean; data: FactureFournisseur }> {
    return this.http.get<{ success: boolean; data: FactureFournisseur }>(`${this.fournisseursUrl}/${id}`);
  }

  createFournisseur(data: Partial<FactureFournisseur>): Observable<{ success: boolean; data: FactureFournisseur; message: string }> {
    return this.http.post<{ success: boolean; data: FactureFournisseur; message: string }>(this.fournisseursUrl, data);
  }

  updateFournisseur(id: number, data: Partial<FactureFournisseur>): Observable<{ success: boolean; data: FactureFournisseur; message: string }> {
    return this.http.put<{ success: boolean; data: FactureFournisseur; message: string }>(`${this.fournisseursUrl}/${id}`, data);
  }

  deleteFournisseur(id: number): Observable<{ success: boolean; message: string }> {
    return this.http.delete<{ success: boolean; message: string }>(`${this.fournisseursUrl}/${id}`);
  }

  impayeesFournisseurs(): Observable<{ success: boolean; data: FactureFournisseur[] }> {
    return this.http.get<{ success: boolean; data: FactureFournisseur[] }>(`${this.fournisseursUrl}/impayees/liste`);
  }

  genererPDFFournisseur(id: number): Observable<Blob> {
    return this.http.get(`${this.fournisseursUrl}/${id}/generer-pdf`, { responseType: 'blob' });
  }
}
