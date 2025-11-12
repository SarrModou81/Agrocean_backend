import { Injectable } from '@angular/core';
import { HttpClient, HttpParams } from '@angular/common/http';
import { Observable } from 'rxjs';
import { environment } from '../../../environments/environment';
import { Categorie, PaginatedResponse } from '../models';

@Injectable({
  providedIn: 'root'
})
export class CategoriesService {
  private apiUrl = `${environment.apiUrl}/categories`;

  constructor(private http: HttpClient) {}

  getAll(params?: any): Observable<PaginatedResponse<Categorie>> {
    let httpParams = new HttpParams();
    if (params) {
      Object.keys(params).forEach(key => {
        if (params[key] !== null && params[key] !== undefined) {
          httpParams = httpParams.set(key, params[key]);
        }
      });
    }
    return this.http.get<PaginatedResponse<Categorie>>(this.apiUrl, { params: httpParams });
  }

  getById(id: number): Observable<{ success: boolean; data: Categorie }> {
    return this.http.get<{ success: boolean; data: Categorie }>(`${this.apiUrl}/${id}`);
  }

  create(data: Partial<Categorie>): Observable<{ success: boolean; data: Categorie; message: string }> {
    return this.http.post<{ success: boolean; data: Categorie; message: string }>(this.apiUrl, data);
  }

  update(id: number, data: Partial<Categorie>): Observable<{ success: boolean; data: Categorie; message: string }> {
    return this.http.put<{ success: boolean; data: Categorie; message: string }>(`${this.apiUrl}/${id}`, data);
  }

  delete(id: number): Observable<{ success: boolean; message: string }> {
    return this.http.delete<{ success: boolean; message: string }>(`${this.apiUrl}/${id}`);
  }

  search(query: string): Observable<PaginatedResponse<Categorie>> {
    return this.http.get<PaginatedResponse<Categorie>>(`${this.apiUrl}?search=${query}`);
  }
}
