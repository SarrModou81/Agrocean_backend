import { Injectable } from '@angular/core';
import { HttpClient, HttpParams } from '@angular/common/http';
import { Observable } from 'rxjs';
import { environment } from '../../../environments/environment';
import { Produit, Categorie, PaginatedResponse } from '../models';

@Injectable({
  providedIn: 'root'
})
export class ProduitsService {
  private apiUrl = `${environment.apiUrl}/produits`;
  private categoriesUrl = `${environment.apiUrl}/categories`;

  constructor(private http: HttpClient) {}

  // Produits
  getAll(params?: any): Observable<PaginatedResponse<Produit>> {
    let httpParams = new HttpParams();
    if (params) {
      Object.keys(params).forEach(key => {
        if (params[key] !== null && params[key] !== undefined) {
          httpParams = httpParams.set(key, params[key]);
        }
      });
    }
    return this.http.get<PaginatedResponse<Produit>>(this.apiUrl, { params: httpParams });
  }

  getById(id: number): Observable<{ success: boolean; data: Produit }> {
    return this.http.get<{ success: boolean; data: Produit }>(`${this.apiUrl}/${id}`);
  }

  create(data: Partial<Produit>): Observable<{ success: boolean; data: Produit; message: string }> {
    return this.http.post<{ success: boolean; data: Produit; message: string }>(this.apiUrl, data);
  }

  update(id: number, data: Partial<Produit>): Observable<{ success: boolean; data: Produit; message: string }> {
    return this.http.put<{ success: boolean; data: Produit; message: string }>(`${this.apiUrl}/${id}`, data);
  }

  delete(id: number): Observable<{ success: boolean; message: string }> {
    return this.http.delete<{ success: boolean; message: string }>(`${this.apiUrl}/${id}`);
  }

  search(query: string): Observable<PaginatedResponse<Produit>> {
    return this.http.get<PaginatedResponse<Produit>>(`${this.apiUrl}?search=${query}`);
  }

  // Catégories
  getAllCategories(): Observable<PaginatedResponse<Categorie>> {
    return this.http.get<PaginatedResponse<Categorie>>(this.categoriesUrl);
  }

  getCategoryById(id: number): Observable<{ success: boolean; data: Categorie }> {
    return this.http.get<{ success: boolean; data: Categorie }>(`${this.categoriesUrl}/${id}`);
  }

  createCategory(data: Partial<Categorie>): Observable<{ success: boolean; data: Categorie; message: string }> {
    return this.http.post<{ success: boolean; data: Categorie; message: string }>(this.categoriesUrl, data);
  }

  updateCategory(id: number, data: Partial<Categorie>): Observable<{ success: boolean; data: Categorie; message: string }> {
    return this.http.put<{ success: boolean; data: Categorie; message: string }>(`${this.categoriesUrl}/${id}`, data);
  }

  deleteCategory(id: number): Observable<{ success: boolean; message: string }> {
    return this.http.delete<{ success: boolean; message: string }>(`${this.categoriesUrl}/${id}`);
  }
}
