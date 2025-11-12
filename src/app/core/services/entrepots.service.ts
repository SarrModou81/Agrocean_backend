import { Injectable } from '@angular/core';
import { HttpClient, HttpParams } from '@angular/common/http';
import { Observable } from 'rxjs';
import { environment } from '../../../environments/environment';
import { Entrepot, PaginatedResponse } from '../models';

@Injectable({
  providedIn: 'root'
})
export class EntrepotsService {
  private apiUrl = `${environment.apiUrl}/entrepots`;

  constructor(private http: HttpClient) {}

  getAll(params?: any): Observable<PaginatedResponse<Entrepot>> {
    let httpParams = new HttpParams();
    if (params) {
      Object.keys(params).forEach(key => {
        if (params[key] !== null && params[key] !== undefined) {
          httpParams = httpParams.set(key, params[key]);
        }
      });
    }
    return this.http.get<PaginatedResponse<Entrepot>>(this.apiUrl, { params: httpParams });
  }

  getById(id: number): Observable<{ success: boolean; data: Entrepot }> {
    return this.http.get<{ success: boolean; data: Entrepot }>(`${this.apiUrl}/${id}`);
  }

  create(data: Partial<Entrepot>): Observable<{ success: boolean; data: Entrepot; message: string }> {
    return this.http.post<{ success: boolean; data: Entrepot; message: string }>(this.apiUrl, data);
  }

  update(id: number, data: Partial<Entrepot>): Observable<{ success: boolean; data: Entrepot; message: string }> {
    return this.http.put<{ success: boolean; data: Entrepot; message: string }>(`${this.apiUrl}/${id}`, data);
  }

  delete(id: number): Observable<{ success: boolean; message: string }> {
    return this.http.delete<{ success: boolean; message: string }>(`${this.apiUrl}/${id}`);
  }

  search(query: string): Observable<PaginatedResponse<Entrepot>> {
    return this.http.get<PaginatedResponse<Entrepot>>(`${this.apiUrl}?search=${query}`);
  }
}
