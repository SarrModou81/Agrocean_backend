import { Injectable } from '@angular/core';
import { HttpClient, HttpParams } from '@angular/common/http';
import { Observable } from 'rxjs';
import { environment } from '../../../environments/environment';
import { CommandeAchat, PaginatedResponse } from '../models';

@Injectable({
  providedIn: 'root'
})
export class CommandesAchatService {
  private apiUrl = `${environment.apiUrl}/commandes-achat`;

  constructor(private http: HttpClient) {}

  getAll(params?: any): Observable<PaginatedResponse<CommandeAchat>> {
    let httpParams = new HttpParams();
    if (params) {
      Object.keys(params).forEach(key => {
        if (params[key] !== null && params[key] !== undefined) {
          httpParams = httpParams.set(key, params[key]);
        }
      });
    }
    return this.http.get<PaginatedResponse<CommandeAchat>>(this.apiUrl, { params: httpParams });
  }

  getById(id: number): Observable<{ success: boolean; data: CommandeAchat }> {
    return this.http.get<{ success: boolean; data: CommandeAchat }>(`${this.apiUrl}/${id}`);
  }

  create(data: Partial<CommandeAchat>): Observable<{ success: boolean; data: CommandeAchat; message: string }> {
    return this.http.post<{ success: boolean; data: CommandeAchat; message: string }>(this.apiUrl, data);
  }

  update(id: number, data: Partial<CommandeAchat>): Observable<{ success: boolean; data: CommandeAchat; message: string }> {
    return this.http.put<{ success: boolean; data: CommandeAchat; message: string }>(`${this.apiUrl}/${id}`, data);
  }

  delete(id: number): Observable<{ success: boolean; message: string }> {
    return this.http.delete<{ success: boolean; message: string }>(`${this.apiUrl}/${id}`);
  }

  valider(id: number): Observable<{ success: boolean; message: string }> {
    return this.http.post<{ success: boolean; message: string }>(`${this.apiUrl}/${id}/valider`, {});
  }

  receptionner(id: number, data?: any): Observable<{ success: boolean; message: string }> {
    return this.http.post<{ success: boolean; message: string }>(`${this.apiUrl}/${id}/receptionner`, data || {});
  }

  annuler(id: number, motif: string): Observable<{ success: boolean; message: string }> {
    return this.http.post<{ success: boolean; message: string }>(`${this.apiUrl}/${id}/annuler`, { motif });
  }
}
