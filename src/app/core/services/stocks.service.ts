import { Injectable } from '@angular/core';
import { HttpClient, HttpParams } from '@angular/common/http';
import { Observable } from 'rxjs';
import { environment } from '../../../environments/environment';
import { Stock, MouvementStock, Entrepot, PaginatedResponse } from '../models';

@Injectable({
  providedIn: 'root'
})
export class StocksService {
  private apiUrl = `${environment.apiUrl}/stocks`;
  private mouvementsUrl = `${environment.apiUrl}/mouvements-stock`;
  private entrepotsUrl = `${environment.apiUrl}/entrepots`;

  constructor(private http: HttpClient) {}

  // Stocks
  getAll(params?: any): Observable<PaginatedResponse<Stock>> {
    let httpParams = new HttpParams();
    if (params) {
      Object.keys(params).forEach(key => {
        if (params[key] !== null && params[key] !== undefined) {
          httpParams = httpParams.set(key, params[key]);
        }
      });
    }
    return this.http.get<PaginatedResponse<Stock>>(this.apiUrl, { params: httpParams });
  }

  getById(id: number): Observable<{ success: boolean; data: Stock }> {
    return this.http.get<{ success: boolean; data: Stock }>(`${this.apiUrl}/${id}`);
  }

  create(data: Partial<Stock>): Observable<{ success: boolean; data: Stock; message: string }> {
    return this.http.post<{ success: boolean; data: Stock; message: string }>(this.apiUrl, data);
  }

  update(id: number, data: Partial<Stock>): Observable<{ success: boolean; data: Stock; message: string }> {
    return this.http.put<{ success: boolean; data: Stock; message: string }>(`${this.apiUrl}/${id}`, data);
  }

  delete(id: number): Observable<{ success: boolean; message: string }> {
    return this.http.delete<{ success: boolean; message: string }>(`${this.apiUrl}/${id}`);
  }

  ajuster(id: number, data: { quantite: number; type: string; raison: string }): Observable<{ success: boolean; data: Stock; message: string }> {
    return this.http.post<{ success: boolean; data: Stock; message: string }>(`${this.apiUrl}/${id}/ajuster`, data);
  }

  transferer(data: { stock_id: number; entrepot_destination_id: number; quantite: number }): Observable<{ success: boolean; message: string }> {
    return this.http.post<{ success: boolean; message: string }>(`${this.apiUrl}/transferer`, data);
  }

  getAlertes(): Observable<{ success: boolean; data: Stock[] }> {
    return this.http.get<{ success: boolean; data: Stock[] }>(`${this.apiUrl}/alertes`);
  }

  // Mouvements de stock
  getAllMouvements(params?: any): Observable<PaginatedResponse<MouvementStock>> {
    let httpParams = new HttpParams();
    if (params) {
      Object.keys(params).forEach(key => {
        if (params[key] !== null && params[key] !== undefined) {
          httpParams = httpParams.set(key, params[key]);
        }
      });
    }
    return this.http.get<PaginatedResponse<MouvementStock>>(this.mouvementsUrl, { params: httpParams });
  }

  getMouvementById(id: number): Observable<{ success: boolean; data: MouvementStock }> {
    return this.http.get<{ success: boolean; data: MouvementStock }>(`${this.mouvementsUrl}/${id}`);
  }

  // Entrepôts
  getAllEntrepots(): Observable<PaginatedResponse<Entrepot>> {
    return this.http.get<PaginatedResponse<Entrepot>>(this.entrepotsUrl);
  }

  getEntrepotById(id: number): Observable<{ success: boolean; data: Entrepot }> {
    return this.http.get<{ success: boolean; data: Entrepot }>(`${this.entrepotsUrl}/${id}`);
  }

  createEntrepot(data: Partial<Entrepot>): Observable<{ success: boolean; data: Entrepot; message: string }> {
    return this.http.post<{ success: boolean; data: Entrepot; message: string }>(this.entrepotsUrl, data);
  }

  updateEntrepot(id: number, data: Partial<Entrepot>): Observable<{ success: boolean; data: Entrepot; message: string }> {
    return this.http.put<{ success: boolean; data: Entrepot; message: string }>(`${this.entrepotsUrl}/${id}`, data);
  }

  deleteEntrepot(id: number): Observable<{ success: boolean; message: string }> {
    return this.http.delete<{ success: boolean; message: string }>(`${this.entrepotsUrl}/${id}`);
  }
}
