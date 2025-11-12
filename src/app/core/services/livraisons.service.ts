import { Injectable } from '@angular/core';
import { HttpClient, HttpParams } from '@angular/common/http';
import { Observable } from 'rxjs';
import { environment } from '../../../environments/environment';
import { Livraison, PaginatedResponse } from '../models';

@Injectable({
  providedIn: 'root'
})
export class LivraisonsService {
  private apiUrl = `${environment.apiUrl}/livraisons`;

  constructor(private http: HttpClient) {}

  getAll(params?: any): Observable<PaginatedResponse<Livraison>> {
    let httpParams = new HttpParams();
    if (params) {
      Object.keys(params).forEach(key => {
        if (params[key] !== null && params[key] !== undefined) {
          httpParams = httpParams.set(key, params[key]);
        }
      });
    }
    return this.http.get<PaginatedResponse<Livraison>>(this.apiUrl, { params: httpParams });
  }

  getById(id: number): Observable<{ success: boolean; data: Livraison }> {
    return this.http.get<{ success: boolean; data: Livraison }>(`${this.apiUrl}/${id}`);
  }

  create(data: Partial<Livraison>): Observable<{ success: boolean; data: Livraison; message: string }> {
    return this.http.post<{ success: boolean; data: Livraison; message: string }>(this.apiUrl, data);
  }

  update(id: number, data: Partial<Livraison>): Observable<{ success: boolean; data: Livraison; message: string }> {
    return this.http.put<{ success: boolean; data: Livraison; message: string }>(`${this.apiUrl}/${id}`, data);
  }

  delete(id: number): Observable<{ success: boolean; message: string }> {
    return this.http.delete<{ success: boolean; message: string }>(`${this.apiUrl}/${id}`);
  }

  demarrer(id: number): Observable<{ success: boolean; message: string }> {
    return this.http.post<{ success: boolean; message: string }>(`${this.apiUrl}/${id}/demarrer`, {});
  }

  confirmer(id: number, data: any): Observable<{ success: boolean; message: string }> {
    return this.http.post<{ success: boolean; message: string }>(`${this.apiUrl}/${id}/confirmer`, data);
  }

  annuler(id: number, motif: string): Observable<{ success: boolean; message: string }> {
    return this.http.post<{ success: boolean; message: string }>(`${this.apiUrl}/${id}/annuler`, { motif });
  }

  aujourdhui(): Observable<{ success: boolean; data: Livraison[] }> {
    return this.http.get<{ success: boolean; data: Livraison[] }>(`${this.apiUrl}/aujourd-hui/liste`);
  }

  statistiques(): Observable<any> {
    return this.http.get<any>(`${this.apiUrl}/statistiques/analyse`);
  }

  genererBonLivraison(id: number): Observable<Blob> {
    return this.http.get(`${this.apiUrl}/${id}/bon-livraison`, { responseType: 'blob' });
  }
}
