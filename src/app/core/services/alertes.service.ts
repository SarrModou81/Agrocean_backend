import { Injectable } from '@angular/core';
import { HttpClient, HttpParams } from '@angular/common/http';
import { Observable } from 'rxjs';
import { environment } from '../../../environments/environment';
import { Alerte, PaginatedResponse } from '../models';

@Injectable({
  providedIn: 'root'
})
export class AlertesService {
  private apiUrl = `${environment.apiUrl}/alertes`;

  constructor(private http: HttpClient) {}

  getAll(params?: any): Observable<PaginatedResponse<Alerte>> {
    let httpParams = new HttpParams();
    if (params) {
      Object.keys(params).forEach(key => {
        if (params[key] !== null && params[key] !== undefined) {
          httpParams = httpParams.set(key, params[key]);
        }
      });
    }
    return this.http.get<PaginatedResponse<Alerte>>(this.apiUrl, { params: httpParams });
  }

  getNonLuesCount(): Observable<{ success: boolean; count: number }> {
    return this.http.get<{ success: boolean; count: number }>(`${this.apiUrl}/non-lues/count`);
  }

  marquerLue(id: number): Observable<{ success: boolean; message: string }> {
    return this.http.post<{ success: boolean; message: string }>(`${this.apiUrl}/${id}/lire`, {});
  }

  marquerToutesLues(): Observable<{ success: boolean; message: string }> {
    return this.http.post<{ success: boolean; message: string }>(`${this.apiUrl}/tout-lire`, {});
  }

  delete(id: number): Observable<{ success: boolean; message: string }> {
    return this.http.delete<{ success: boolean; message: string }>(`${this.apiUrl}/${id}`);
  }
}
