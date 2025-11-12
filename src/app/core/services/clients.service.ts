import { Injectable } from '@angular/core';
import { HttpClient, HttpParams } from '@angular/common/http';
import { Observable } from 'rxjs';
import { environment } from '../../../environments/environment';
import { Client, PaginatedResponse } from '../models';

@Injectable({
  providedIn: 'root'
})
export class ClientsService {
  private apiUrl = `${environment.apiUrl}/clients`;

  constructor(private http: HttpClient) {}

  getAll(params?: any): Observable<PaginatedResponse<Client>> {
    let httpParams = new HttpParams();
    if (params) {
      Object.keys(params).forEach(key => {
        if (params[key] !== null && params[key] !== undefined) {
          httpParams = httpParams.set(key, params[key]);
        }
      });
    }
    return this.http.get<PaginatedResponse<Client>>(this.apiUrl, { params: httpParams });
  }

  getById(id: number): Observable<{ success: boolean; data: Client }> {
    return this.http.get<{ success: boolean; data: Client }>(`${this.apiUrl}/${id}`);
  }

  create(data: Partial<Client>): Observable<{ success: boolean; data: Client; message: string }> {
    return this.http.post<{ success: boolean; data: Client; message: string }>(this.apiUrl, data);
  }

  update(id: number, data: Partial<Client>): Observable<{ success: boolean; data: Client; message: string }> {
    return this.http.put<{ success: boolean; data: Client; message: string }>(`${this.apiUrl}/${id}`, data);
  }

  delete(id: number): Observable<{ success: boolean; message: string }> {
    return this.http.delete<{ success: boolean; message: string }>(`${this.apiUrl}/${id}`);
  }

  getHistorique(id: number): Observable<any> {
    return this.http.get<any>(`${this.apiUrl}/${id}/historique`);
  }

  search(query: string): Observable<PaginatedResponse<Client>> {
    return this.http.get<PaginatedResponse<Client>>(`${this.apiUrl}?search=${query}`);
  }
}
