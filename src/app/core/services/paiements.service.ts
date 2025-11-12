import { Injectable } from '@angular/core';
import { HttpClient, HttpParams } from '@angular/common/http';
import { Observable } from 'rxjs';
import { environment } from '../../../environments/environment';
import { Paiement, PaginatedResponse } from '../models';

@Injectable({
  providedIn: 'root'
})
export class PaiementsService {
  private apiUrl = `${environment.apiUrl}/paiements`;

  constructor(private http: HttpClient) {}

  getAll(params?: any): Observable<PaginatedResponse<Paiement>> {
    let httpParams = new HttpParams();
    if (params) {
      Object.keys(params).forEach(key => {
        if (params[key] !== null && params[key] !== undefined) {
          httpParams = httpParams.set(key, params[key]);
        }
      });
    }
    return this.http.get<PaginatedResponse<Paiement>>(this.apiUrl, { params: httpParams });
  }

  getById(id: number): Observable<{ success: boolean; data: Paiement }> {
    return this.http.get<{ success: boolean; data: Paiement }>(`${this.apiUrl}/${id}`);
  }

  create(data: Partial<Paiement>): Observable<{ success: boolean; data: Paiement; message: string }> {
    return this.http.post<{ success: boolean; data: Paiement; message: string }>(this.apiUrl, data);
  }

  update(id: number, data: Partial<Paiement>): Observable<{ success: boolean; data: Paiement; message: string }> {
    return this.http.put<{ success: boolean; data: Paiement; message: string }>(`${this.apiUrl}/${id}`, data);
  }

  delete(id: number): Observable<{ success: boolean; message: string }> {
    return this.http.delete<{ success: boolean; message: string }>(`${this.apiUrl}/${id}`);
  }

  statistiques(): Observable<any> {
    return this.http.get<any>(`${this.apiUrl}/statistiques/analyse`);
  }
}
