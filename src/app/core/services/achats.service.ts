import { Injectable } from '@angular/core';
import { HttpClient, HttpParams } from '@angular/common/http';
import { Observable } from 'rxjs';
import { environment } from '../../../environments/environment';
import { CommandeAchat, Fournisseur, PaginatedResponse } from '../models';

@Injectable({
  providedIn: 'root'
})
export class AchatsService {
  private apiUrl = `${environment.apiUrl}/commandes-achat`;
  private fournisseursUrl = `${environment.apiUrl}/fournisseurs`;

  constructor(private http: HttpClient) {}

  // Commandes d'achat
  getAll(params?: any): Observable<PaginatedResponse<CommandeAchat>> {
    let httpParams = new HttpParams();
    if (params) {
      Object.keys(params).forEach(key => {
        if (params[key] !== null && params[key] !== undefined) {
          httpParams = httpParams.set(key, params[key]);
        }
      });
    }
    return this.http.get<PaginatedResponse<CommandeAchat>>(this.apiUrl, { params: httpParams });
  }

  getById(id: number): Observable<{ success: boolean; data: CommandeAchat }> {
    return this.http.get<{ success: boolean; data: CommandeAchat }>(`${this.apiUrl}/${id}`);
  }

  create(data: any): Observable<{ success: boolean; data: CommandeAchat; message: string }> {
    return this.http.post<{ success: boolean; data: CommandeAchat; message: string }>(this.apiUrl, data);
  }

  update(id: number, data: any): Observable<{ success: boolean; data: CommandeAchat; message: string }> {
    return this.http.put<{ success: boolean; data: CommandeAchat; message: string }>(`${this.apiUrl}/${id}`, data);
  }

  delete(id: number): Observable<{ success: boolean; message: string }> {
    return this.http.delete<{ success: boolean; message: string }>(`${this.apiUrl}/${id}`);
  }

  valider(id: number): Observable<{ success: boolean; data: CommandeAchat; message: string }> {
    return this.http.post<{ success: boolean; data: CommandeAchat; message: string }>(`${this.apiUrl}/${id}/valider`, {});
  }

  annuler(id: number, data: { raison_annulation: string }): Observable<{ success: boolean; data: CommandeAchat; message: string }> {
    return this.http.post<{ success: boolean; data: CommandeAchat; message: string }>(`${this.apiUrl}/${id}/annuler`, data);
  }

  recevoir(id: number, data: any): Observable<{ success: boolean; data: CommandeAchat; message: string }> {
    return this.http.post<{ success: boolean; data: CommandeAchat; message: string }>(`${this.apiUrl}/${id}/recevoir`, data);
  }

  genererPDF(id: number): Observable<Blob> {
    return this.http.get(`${this.apiUrl}/${id}/pdf`, { responseType: 'blob' });
  }

  // Fournisseurs
  getAllFournisseurs(params?: any): Observable<PaginatedResponse<Fournisseur>> {
    let httpParams = new HttpParams();
    if (params) {
      Object.keys(params).forEach(key => {
        if (params[key] !== null && params[key] !== undefined) {
          httpParams = httpParams.set(key, params[key]);
        }
      });
    }
    return this.http.get<PaginatedResponse<Fournisseur>>(this.fournisseursUrl, { params: httpParams });
  }

  getFournisseurById(id: number): Observable<{ success: boolean; data: Fournisseur }> {
    return this.http.get<{ success: boolean; data: Fournisseur }>(`${this.fournisseursUrl}/${id}`);
  }

  createFournisseur(data: Partial<Fournisseur>): Observable<{ success: boolean; data: Fournisseur; message: string }> {
    return this.http.post<{ success: boolean; data: Fournisseur; message: string }>(this.fournisseursUrl, data);
  }

  updateFournisseur(id: number, data: Partial<Fournisseur>): Observable<{ success: boolean; data: Fournisseur; message: string }> {
    return this.http.put<{ success: boolean; data: Fournisseur; message: string }>(`${this.fournisseursUrl}/${id}`, data);
  }

  deleteFournisseur(id: number): Observable<{ success: boolean; message: string }> {
    return this.http.delete<{ success: boolean; message: string }>(`${this.fournisseursUrl}/${id}`);
  }
}
