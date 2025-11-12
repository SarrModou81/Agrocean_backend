# AGROCEAN - Frontend Angular 17

Application frontend complète pour la gestion d'inventaire et de ventes AGROCEAN.

## 🚀 Fonctionnalités Implémentées

### ✅ Modules Complets

#### 1. **Module d'Authentification**
- Login avec JWT
- Register avec validation
- Gestion sécurisée des tokens
- Guards de protection des routes
- Interceptors HTTP (JWT, Error)

#### 2. **Module Dashboard**
- Tableau de bord avec statistiques
- 4 cartes de statistiques en temps réel
- Navigation rapide vers tous les modules
- Actions rapides (Nouvelle vente, Ajouter produit, etc.)
- 6 cartes de navigation modulaire

#### 3. **Module Produits** (Complet)
- **Liste** :
  - Tableau paginé avec recherche
  - Tri par colonnes
  - Actions CRUD (Créer, Modifier, Supprimer)
  - Indicateur stock faible
- **Formulaire** :
  - Validation complète
  - Support catégories
  - Prix achat/vente
  - Seuils min/max
  - 7 unités de mesure

#### 4. **Module Clients** (Complet)
- **Liste** :
  - Tableau paginé
  - Recherche par nom/email
  - Actions CRUD
- **Formulaire** :
  - Nom, Email, Téléphone
  - Adresse, Ville, Pays
  - Validation

### 🛠️ Services API (4 Services)

1. **ProduitsService** - CRUD produits et catégories
2. **StocksService** - Gestion stocks, mouvements, entrepôts, alertes
3. **VentesService** - Ventes, clients, livraisons, factures
4. **AchatsService** - Commandes d'achat, fournisseurs

### 🎨 Composants Partagés

- **ConfirmDialogComponent** - Confirmation d'actions
- **LoadingSpinnerComponent** - Spinner de chargement
- **SharedModule** - Modules Material réutilisables

## 📦 Technologies Utilisées

- **Angular 17.3.17** (mode modules)
- **Angular Material 17**
- **TypeScript 5.x**
- **RxJS** pour programmation réactive
- **ngx-toastr** pour notifications
- **jwt-decode** pour gestion tokens

## 🏗️ Architecture

```
src/app/
├── core/                    # Services, guards, interceptors, modèles
│   ├── guards/              # AuthGuard, RoleGuard
│   ├── interceptors/        # JwtInterceptor, ErrorInterceptor
│   ├── models/              # 17 interfaces TypeScript
│   └── services/            # 5 services (Auth, Produits, Stocks, Ventes, Achats)
├── features/                # Modules fonctionnels
│   ├── auth/                # Login, Register
│   ├── dashboard/           # Tableau de bord
│   ├── produits/            # Gestion produits
│   └── clients/             # Gestion clients
├── shared/                  # Composants réutilisables
│   └── components/          # ConfirmDialog, LoadingSpinner
└── app-routing.module.ts    # Routing principal avec lazy loading
```

## 🚦 Démarrage

### Prérequis
- Node.js 18+ et npm
- Angular CLI 17

### Installation

```bash
cd Agrocean_frontend
npm install
```

### Lancement

```bash
ng serve
```

Application disponible sur **http://localhost:4200**

### Build de production

```bash
ng build --configuration=production
```

## 🔐 Configuration API

Modifier `src/environments/environment.ts` :

```typescript
export const environment = {
  production: false,
  apiUrl: 'http://localhost:8000/api'  // URL de votre backend Laravel
};
```

## 🎯 Modules à Développer

Les services API sont prêts pour ces modules :

- ✅ **Produits** - Complet
- ✅ **Clients** - Complet
- ⏳ **Stocks** - Service prêt, UI à créer
- ⏳ **Ventes** - Service prêt, UI à créer
- ⏳ **Achats** - Service prêt, UI à créer
- ⏳ **Fournisseurs** - Service prêt, UI à créer
- ⏳ **Rapports** - À créer

## 📝 Pattern de Développement

Pour créer un nouveau module (ex: Stocks) :

```bash
# 1. Générer le module
ng generate module features/stocks --routing
ng generate component features/stocks/liste
ng generate component features/stocks/form

# 2. Configurer le module (utiliser SharedModule)
# 3. Créer les routes
# 4. Implémenter liste + formulaire (suivre pattern Produits)
# 5. Ajouter au routing principal (app-routing.module.ts)
```

## 🔒 Rôles Utilisateur

- **Administrateur** - Accès complet
- **Commercial** - Ventes, clients
- **GestionnaireStock** - Stocks, produits
- **Comptable** - Factures, paiements
- **AgentApprovisionnement** - Achats, fournisseurs

## 📊 Fonctionnalités Backend Disponibles

Le backend Laravel offre :
- CRUD complet pour tous les modules
- Prévisions de réapprovisionnement (IA)
- Traçabilité complète des lots
- Génération PDF (factures, bons de livraison, etc.)
- Export Excel/CSV
- RBAC (Role-Based Access Control)
- 260+ endpoints API

## 🌐 URLs de l'Application

- **Login** : `/auth/login`
- **Register** : `/auth/register`
- **Dashboard** : `/dashboard`
- **Produits** : `/produits`
- **Clients** : `/clients`

## 🎨 Design

- Interface Material Design
- Responsive (mobile, tablette, desktop)
- Animations et transitions fluides
- Thème personnalisé AGROCEAN

## 📧 Support

Pour toute question ou problème, consulter la documentation Angular ou Laravel.

## 🚀 Prochaines Étapes Recommandées

1. Implémenter les modules restants (Stocks, Ventes, Achats, Fournisseurs)
2. Ajouter les tests unitaires
3. Ajouter les tests E2E
4. Optimiser les performances
5. Ajouter l'internationalisation (i18n)
6. Déployer en production

---

**Développé avec ❤️ pour AGROCEAN**
