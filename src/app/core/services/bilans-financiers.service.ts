import { Injectable } from '@angular/core';
import { HttpClient, HttpParams } from '@angular/common/http';
import { Observable } from 'rxjs';
import { environment } from '../../../environments/environment';
import { BilanFinancier, EtatTresorerie, CompteResultat, DashboardFinancier, PaginatedResponse } from '../models';

@Injectable({
  providedIn: 'root'
})
export class BilansFinanciersService {
  private apiUrl = `${environment.apiUrl}/bilans`;

  constructor(private http: HttpClient) {}

  getAll(params?: any): Observable<PaginatedResponse<BilanFinancier>> {
    let httpParams = new HttpParams();
    if (params) {
      Object.keys(params).forEach(key => {
        if (params[key] !== null && params[key] !== undefined) {
          httpParams = httpParams.set(key, params[key]);
        }
      });
    }
    return this.http.get<PaginatedResponse<BilanFinancier>>(this.apiUrl, { params: httpParams });
  }

  getById(id: number): Observable<{ success: boolean; data: BilanFinancier }> {
    return this.http.get<{ success: boolean; data: BilanFinancier }>(`${this.apiUrl}/${id}`);
  }

  generer(data: any): Observable<{ success: boolean; data: BilanFinancier; message: string }> {
    return this.http.post<{ success: boolean; data: BilanFinancier; message: string }>(`${this.apiUrl}/generer`, data);
  }

  etatTresorerie(): Observable<{ success: boolean; data: EtatTresorerie }> {
    return this.http.get<{ success: boolean; data: EtatTresorerie }>(`${this.apiUrl}/tresorerie/etat`);
  }

  compteResultat(params?: any): Observable<{ success: boolean; data: CompteResultat }> {
    let httpParams = new HttpParams();
    if (params) {
      Object.keys(params).forEach(key => {
        if (params[key] !== null && params[key] !== undefined) {
          httpParams = httpParams.set(key, params[key]);
        }
      });
    }
    return this.http.get<{ success: boolean; data: CompteResultat }>(`${this.apiUrl}/compte-resultat`, { params: httpParams });
  }

  bilanComptable(params?: any): Observable<any> {
    let httpParams = new HttpParams();
    if (params) {
      Object.keys(params).forEach(key => {
        if (params[key] !== null && params[key] !== undefined) {
          httpParams = httpParams.set(key, params[key]);
        }
      });
    }
    return this.http.get<any>(`${this.apiUrl}/bilan-comptable`, { params: httpParams });
  }

  dashboardFinancier(): Observable<{ success: boolean; data: DashboardFinancier }> {
    return this.http.get<{ success: boolean; data: DashboardFinancier }>(`${this.apiUrl}/dashboard-financier`);
  }
}
