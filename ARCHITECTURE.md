# 🏗️ Architecture Frontend AGROCEAN - Angular 17

**Date:** 12 Novembre 2025
**Version:** 1.0.0
**Framework:** Angular 17 (Mode Modules)

---

## 📋 Vue d'Ensemble

Application frontend Angular 17 pour la plateforme de gestion des stocks AGROCEAN.

### Technologies
- **Angular** 17.3.17 (standalone=false)
- **Angular Material** 17
- **SCSS** pour les styles
- **TypeScript** 5.x
- **RxJS** pour la programmation réactive
- **JWT** pour l'authentification

---

## 📁 Structure du Projet

```
Agrocean_frontend/
├── src/
│   ├── app/
│   │   ├── core/                    # Fonctionnalités centrales
│   │   │   ├── guards/              # Guards de routing
│   │   │   │   ├── auth.guard.ts    # Protection des routes
│   │   │   │   └── role.guard.ts    # Vérification des rôles
│   │   │   ├── interceptors/        # Interceptors HTTP
│   │   │   │   ├── jwt.interceptor.ts    # Injection du token
│   │   │   │   └── error.interceptor.ts  # Gestion des erreurs
│   │   │   ├── models/              # Interfaces TypeScript
│   │   │   │   ├── user.model.ts         # Modèles utilisateur
│   │   │   │   ├── produit.model.ts      # Modèles produit/stock
│   │   │   │   ├── vente.model.ts        # Modèles vente
│   │   │   │   ├── achat.model.ts        # Modèles achat
│   │   │   │   └── index.ts              # Exports
│   │   │   └── services/            # Services métier
│   │   │       ├── auth.service.ts       # Authentification
│   │   │       └── storage.service.ts    # LocalStorage
│   │   │
│   │   ├── shared/                  # Composants partagés
│   │   │   ├── components/          # Composants réutilisables
│   │   │   ├── directives/          # Directives personnalisées
│   │   │   └── pipes/               # Pipes personnalisés
│   │   │
│   │   ├── features/                # Modules fonctionnels
│   │   │   ├── auth/                # Authentification
│   │   │   ├── dashboard/           # Tableau de bord
│   │   │   ├── produits/            # Gestion produits
│   │   │   ├── stocks/              # Gestion stocks
│   │   │   ├── ventes/              # Gestion ventes
│   │   │   ├── achats/              # Gestion achats
│   │   │   ├── clients/             # Gestion clients
│   │   │   ├── fournisseurs/        # Gestion fournisseurs
│   │   │   ├── rapports/            # Rapports
│   │   │   └── parametres/          # Paramètres
│   │   │
│   │   ├── app-routing.module.ts   # Routing principal
│   │   ├── app.module.ts            # Module principal
│   │   └── app.component.ts         # Composant racine
│   │
│   ├── environments/               # Configuration environnement
│   │   └── environment.ts          # Config développement
│   │
│   ├── assets/                     # Ressources statiques
│   ├── styles.scss                 # Styles globaux
│   └── index.html                  # Page HTML principale
│
├── angular.json                    # Configuration Angular
├── package.json                    # Dépendances npm
├── tsconfig.json                   # Configuration TypeScript
└── README.md                       # Documentation
```

---

## ✅ Fichiers Créés

### 1. Models (Interfaces TypeScript)
- ✅ `user.model.ts` - User, UserRole, LoginRequest, LoginResponse
- ✅ `produit.model.ts` - Produit, Categorie, Stock, Entrepot, MouvementStock
- ✅ `vente.model.ts` - Vente, Client, DetailVente, Livraison, Facture
- ✅ `achat.model.ts` - CommandeAchat, Fournisseur, FactureFournisseur, Paiement
- ✅ `index.ts` - Exports centralisés

### 2. Services
- ✅ `auth.service.ts` - Authentification JWT
  - login(), logout(), register()
  - refreshToken(), me()
  - isAuthenticated(), hasRole(), hasAnyRole()

- ✅ `storage.service.ts` - Gestion LocalStorage
  - saveToken(), getToken(), removeToken()
  - saveUser(), getUser(), removeUser()

### 3. Guards
- ✅ `auth.guard.ts` - Protection des routes authentifiées
- ✅ `role.guard.ts` - Vérification des rôles utilisateur

### 4. Interceptors
- ✅ `jwt.interceptor.ts` - Injection automatique du token JWT
- ✅ `error.interceptor.ts` - Gestion centralisée des erreurs HTTP

### 5. Configuration
- ✅ `environment.ts` - Configuration API (http://localhost:8000/api)

---

## 🔧 Configuration Requise

### Dépendances Installées
```json
{
  "@angular/animations": "^17.3.17",
  "@angular/common": "^17.3.17",
  "@angular/compiler": "^17.3.17",
  "@angular/core": "^17.3.17",
  "@angular/forms": "^17.3.17",
  "@angular/material": "^17.3.17",
  "@angular/cdk": "^17.3.17",
  "@angular/platform-browser": "^17.3.17",
  "@angular/router": "^17.3.17",
  "ngx-toastr": "^18.0.0",
  "jwt-decode": "^4.0.0",
  "rxjs": "~7.8.0",
  "tslib": "^2.3.0",
  "zone.js": "~0.14.3"
}
```

---

## 🚀 Prochaines Étapes

### Phase 1: Configuration de Base (À faire)

#### 1. Configurer app.module.ts
```typescript
import { HTTP_INTERCEPTORS, HttpClientModule } from '@angular/common/http';
import { BrowserAnimationsModule } from '@angular/platform-browser/animations';
import { JwtInterceptor } from './core/interceptors/jwt.interceptor';
import { ErrorInterceptor } from './core/interceptors/error.interceptor';
import { ToastrModule } from 'ngx-toastr';

@NgModule({
  imports: [
    BrowserModule,
    BrowserAnimationsModule,
    HttpClientModule,
    AppRoutingModule,
    ToastrModule.forRoot()
  ],
  providers: [
    { provide: HTTP_INTERCEPTORS, useClass: JwtInterceptor, multi: true },
    { provide: HTTP_INTERCEPTORS, useClass: ErrorInterceptor, multi: true }
  ]
})
```

#### 2. Créer le module d'authentification
```bash
ng generate module features/auth --routing
ng generate component features/auth/login
ng generate component features/auth/register
```

#### 3. Créer le layout principal
```bash
ng generate component shared/components/header
ng generate component shared/components/sidebar
ng generate component shared/components/footer
```

#### 4. Créer le dashboard
```bash
ng generate module features/dashboard --routing
ng generate component features/dashboard/home
```

### Phase 2: Modules Fonctionnels (À créer)

#### Gestion des Produits
```bash
ng generate module features/produits --routing
ng generate component features/produits/liste
ng generate component features/produits/detail
ng generate component features/produits/form
ng generate service features/produits/produits
```

#### Gestion des Stocks
```bash
ng generate module features/stocks --routing
ng generate component features/stocks/liste
ng generate component features/stocks/mouvements
ng generate component features/stocks/inventaire
ng generate service features/stocks/stocks
```

#### Gestion des Ventes
```bash
ng generate module features/ventes --routing
ng generate component features/ventes/liste
ng generate component features/ventes/nouvelle
ng generate component features/ventes/detail
ng generate service features/ventes/ventes
```

### Phase 3: Composants Partagés (À créer)

```bash
ng generate component shared/components/data-table
ng generate component shared/components/loading-spinner
ng generate component shared/components/confirm-dialog
ng generate pipe shared/pipes/currency-fcfa
ng generate directive shared/directives/has-role
```

---

## 🎨 Styles et Thème

### Angular Material
Le projet utilise Angular Material pour l'UI. Configuration à ajouter :

```scss
// styles.scss
@import '@angular/material/prebuilt-themes/indigo-pink.css';
@import 'ngx-toastr/toastr';

// Variables AGROCEAN
$primary-color: #2c3e50;
$secondary-color: #3498db;
$success-color: #27ae60;
$warning-color: #f39c12;
$danger-color: #e74c3c;
```

---

## 🔐 Authentification

### Flow d'Authentification

1. **Login**
   ```typescript
   this.authService.login({ email, password })
     .subscribe({
       next: (response) => {
         // Token et user stockés automatiquement
         this.router.navigate(['/dashboard']);
       },
       error: (err) => {
         this.toastr.error(err.message);
       }
     });
   ```

2. **Protection des Routes**
   ```typescript
   // app-routing.module.ts
   {
     path: 'dashboard',
     loadChildren: () => import('./features/dashboard/dashboard.module'),
     canActivate: [AuthGuard]
   }
   ```

3. **Vérification des Rôles**
   ```typescript
   {
     path: 'users',
     component: UsersComponent,
     canActivate: [AuthGuard, RoleGuard],
     data: { roles: ['Administrateur'] }
   }
   ```

---

## 📡 Consommation de l'API

### Exemple de Service
```typescript
@Injectable({ providedIn: 'root' })
export class ProduitsService {
  private apiUrl = `${environment.apiUrl}/produits`;

  constructor(private http: HttpClient) {}

  getAll(): Observable<PaginatedResponse<Produit>> {
    return this.http.get<PaginatedResponse<Produit>>(this.apiUrl);
  }

  getById(id: number): Observable<Produit> {
    return this.http.get<Produit>(`${this.apiUrl}/${id}`);
  }

  create(data: Partial<Produit>): Observable<Produit> {
    return this.http.post<Produit>(this.apiUrl, data);
  }

  update(id: number, data: Partial<Produit>): Observable<Produit> {
    return this.http.put<Produit>(`${this.apiUrl}/${id}`, data);
  }

  delete(id: number): Observable<void> {
    return this.http.delete<void>(`${this.apiUrl}/${id}`);
  }
}
```

---

## 🧪 Lancer le Projet

### Développement
```bash
cd Agrocean_frontend
npm install
ng serve
# Application disponible sur http://localhost:4200
```

### Build Production
```bash
ng build --configuration production
# Fichiers dans dist/agrocean-frontend
```

### Tests
```bash
ng test        # Tests unitaires
ng e2e         # Tests end-to-end
```

---

## 📚 Ressources

### Documentation
- [Angular 17](https://angular.io/docs)
- [Angular Material](https://material.angular.io/)
- [RxJS](https://rxjs.dev/)
- [TypeScript](https://www.typescriptlang.org/docs/)

### API Backend
- URL: `http://localhost:8000/api`
- Documentation: Voir `READY_FOR_MIGRATION.md` dans Agrocean_backend

---

## ✅ Checklist de Développement

### Configuration Initiale
- [ ] Configurer app.module.ts avec interceptors
- [ ] Créer le module d'authentification
- [ ] Créer le layout (header, sidebar, footer)
- [ ] Configurer le routing
- [ ] Créer la page de dashboard

### Modules Fonctionnels
- [ ] Module Produits
- [ ] Module Stocks
- [ ] Module Ventes
- [ ] Module Achats
- [ ] Module Clients
- [ ] Module Fournisseurs
- [ ] Module Rapports
- [ ] Module Paramètres

### Composants Partagés
- [ ] DataTable réutilisable
- [ ] Loading Spinner
- [ ] Dialog de confirmation
- [ ] Pipes personnalisés
- [ ] Directives de rôles

### Tests et Déploiement
- [ ] Tests unitaires
- [ ] Tests d'intégration
- [ ] Build de production
- [ ] Déploiement

---

## 🎯 Architecture AGROCEAN

### Modules par Rôle

**Administrateur:**
- Tous les modules accessibles

**Commercial:**
- Dashboard, Ventes, Clients, Livraisons, Factures

**GestionnaireStock:**
- Dashboard, Produits, Stocks, Entrepôts, Inventaire

**Comptable:**
- Dashboard, Factures, Paiements, Rapports Financiers

**AgentApprovisionnement:**
- Dashboard, Achats, Fournisseurs, Réceptions

---

**Status:** 🚀 **FONDATIONS CRÉÉES - PRÊT POUR LE DÉVELOPPEMENT**

Le projet Angular est initialisé avec :
- ✅ Structure de dossiers complète
- ✅ Modèles TypeScript (17 interfaces)
- ✅ Services d'authentification
- ✅ Guards de routing
- ✅ Interceptors HTTP
- ✅ Configuration API

**Prochaine étape:** Créer les modules et composants des fonctionnalités.
