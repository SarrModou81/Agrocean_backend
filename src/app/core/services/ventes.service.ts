import { Injectable } from '@angular/core';
import { HttpClient, HttpParams } from '@angular/common/http';
import { Observable } from 'rxjs';
import { environment } from '../../../environments/environment';
import { Vente, Client, Livraison, Facture, PaginatedResponse } from '../models';

@Injectable({
  providedIn: 'root'
})
export class VentesService {
  private apiUrl = `${environment.apiUrl}/ventes`;
  private clientsUrl = `${environment.apiUrl}/clients`;
  private livraisonsUrl = `${environment.apiUrl}/livraisons`;
  private facturesUrl = `${environment.apiUrl}/factures`;

  constructor(private http: HttpClient) {}

  // Ventes
  getAll(params?: any): Observable<PaginatedResponse<Vente>> {
    let httpParams = new HttpParams();
    if (params) {
      Object.keys(params).forEach(key => {
        if (params[key] !== null && params[key] !== undefined) {
          httpParams = httpParams.set(key, params[key]);
        }
      });
    }
    return this.http.get<PaginatedResponse<Vente>>(this.apiUrl, { params: httpParams });
  }

  getById(id: number): Observable<{ success: boolean; data: Vente }> {
    return this.http.get<{ success: boolean; data: Vente }>(`${this.apiUrl}/${id}`);
  }

  create(data: any): Observable<{ success: boolean; data: Vente; message: string }> {
    return this.http.post<{ success: boolean; data: Vente; message: string }>(this.apiUrl, data);
  }

  update(id: number, data: any): Observable<{ success: boolean; data: Vente; message: string }> {
    return this.http.put<{ success: boolean; data: Vente; message: string }>(`${this.apiUrl}/${id}`, data);
  }

  delete(id: number): Observable<{ success: boolean; message: string }> {
    return this.http.delete<{ success: boolean; message: string }>(`${this.apiUrl}/${id}`);
  }

  valider(id: number): Observable<{ success: boolean; data: Vente; message: string }> {
    return this.http.post<{ success: boolean; data: Vente; message: string }>(`${this.apiUrl}/${id}/valider`, {});
  }

  annuler(id: number, data: { raison: string }): Observable<{ success: boolean; data: Vente; message: string }> {
    return this.http.post<{ success: boolean; data: Vente; message: string }>(`${this.apiUrl}/${id}/annuler`, data);
  }

  genererFacture(id: number): Observable<Blob> {
    return this.http.get(`${this.apiUrl}/${id}/facture/pdf`, { responseType: 'blob' });
  }

  // Clients
  getAllClients(params?: any): Observable<PaginatedResponse<Client>> {
    let httpParams = new HttpParams();
    if (params) {
      Object.keys(params).forEach(key => {
        if (params[key] !== null && params[key] !== undefined) {
          httpParams = httpParams.set(key, params[key]);
        }
      });
    }
    return this.http.get<PaginatedResponse<Client>>(this.clientsUrl, { params: httpParams });
  }

  getClientById(id: number): Observable<{ success: boolean; data: Client }> {
    return this.http.get<{ success: boolean; data: Client }>(`${this.clientsUrl}/${id}`);
  }

  createClient(data: Partial<Client>): Observable<{ success: boolean; data: Client; message: string }> {
    return this.http.post<{ success: boolean; data: Client; message: string }>(this.clientsUrl, data);
  }

  updateClient(id: number, data: Partial<Client>): Observable<{ success: boolean; data: Client; message: string }> {
    return this.http.put<{ success: boolean; data: Client; message: string }>(`${this.clientsUrl}/${id}`, data);
  }

  deleteClient(id: number): Observable<{ success: boolean; message: string }> {
    return this.http.delete<{ success: boolean; message: string }>(`${this.clientsUrl}/${id}`);
  }

  // Livraisons
  getAllLivraisons(params?: any): Observable<PaginatedResponse<Livraison>> {
    let httpParams = new HttpParams();
    if (params) {
      Object.keys(params).forEach(key => {
        if (params[key] !== null && params[key] !== undefined) {
          httpParams = httpParams.set(key, params[key]);
        }
      });
    }
    return this.http.get<PaginatedResponse<Livraison>>(this.livraisonsUrl, { params: httpParams });
  }

  createLivraison(data: Partial<Livraison>): Observable<{ success: boolean; data: Livraison; message: string }> {
    return this.http.post<{ success: boolean; data: Livraison; message: string }>(this.livraisonsUrl, data);
  }

  updateStatutLivraison(id: number, data: { statut: string }): Observable<{ success: boolean; data: Livraison; message: string }> {
    return this.http.post<{ success: boolean; data: Livraison; message: string }>(`${this.livraisonsUrl}/${id}/statut`, data);
  }

  genererBonLivraison(id: number): Observable<Blob> {
    return this.http.get(`${this.livraisonsUrl}/${id}/bon-livraison/pdf`, { responseType: 'blob' });
  }

  // Factures
  getAllFactures(params?: any): Observable<PaginatedResponse<Facture>> {
    let httpParams = new HttpParams();
    if (params) {
      Object.keys(params).forEach(key => {
        if (params[key] !== null && params[key] !== undefined) {
          httpParams = httpParams.set(key, params[key]);
        }
      });
    }
    return this.http.get<PaginatedResponse<Facture>>(this.facturesUrl, { params: httpParams });
  }

  marquerPayee(id: number, data: { mode_paiement: string }): Observable<{ success: boolean; data: Facture; message: string }> {
    return this.http.post<{ success: boolean; data: Facture; message: string }>(`${this.facturesUrl}/${id}/payer`, data);
  }
}
