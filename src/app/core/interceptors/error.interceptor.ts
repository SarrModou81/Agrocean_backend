// core/interceptors/error.interceptor.ts

import { Injectable } from '@angular/core';
import {
  HttpRequest,
  HttpHandler,
  HttpEvent,
  HttpInterceptor,
  HttpErrorResponse
} from '@angular/common/http';
import { Observable, throwError } from 'rxjs';
import { catchError } from 'rxjs/operators';
import { Router } from '@angular/router';
import { AuthService } from '../services/auth.service';

@Injectable()
export class ErrorInterceptor implements HttpInterceptor {
  constructor(
    private router: Router,
    private authService: AuthService
  ) {}

  intercept(request: HttpRequest<unknown>, next: HttpHandler): Observable<HttpEvent<unknown>> {
    return next.handle(request).pipe(
      catchError((error: HttpErrorResponse) => {
        let errorMessage = 'Une erreur est survenue';

        if (error.error instanceof ErrorEvent) {
          // Erreur côté client
          errorMessage = `Erreur: ${error.error.message}`;
        } else {
          // Erreur côté serveur
          switch (error.status) {
            case 401:
              // Non autorisé - déconnecter l'utilisateur
              this.authService.logout().subscribe();
              errorMessage = 'Session expirée. Veuillez vous reconnecter.';
              break;
            case 403:
              errorMessage = 'Accès refusé. Vous n\'avez pas les permissions nécessaires.';
              this.router.navigate(['/unauthorized']);
              break;
            case 404:
              errorMessage = 'Ressource non trouvée.';
              break;
            case 422:
              // Erreurs de validation
              if (error.error.errors) {
                const errors = error.error.errors;
                errorMessage = Object.values(errors).flat().join('\n');
              } else if (error.error.message) {
                errorMessage = error.error.message;
              }
              break;
            case 500:
              errorMessage = 'Erreur serveur. Veuillez réessayer plus tard.';
              break;
            default:
              errorMessage = error.error.message || errorMessage;
          }
        }

        return throwError(() => ({ message: errorMessage, error }));
      })
    );
  }
}
