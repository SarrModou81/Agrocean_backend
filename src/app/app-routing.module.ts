import { NgModule } from '@angular/core';
import { RouterModule, Routes } from '@angular/router';
import { AuthGuard } from './core/guards/auth.guard';
import { MainLayoutComponent } from './core/layouts/main-layout/main-layout.component';

const routes: Routes = [
  { path: '', redirectTo: 'dashboard', pathMatch: 'full' },
  {
    path: 'auth',
    loadChildren: () => import('./features/auth/auth.module').then(m => m.AuthModule)
  },
  {
    path: '',
    component: MainLayoutComponent,
    canActivate: [AuthGuard],
    children: [
      {
        path: 'dashboard',
        loadChildren: () => import('./features/dashboard/dashboard.module').then(m => m.DashboardModule)
      },
      {
        path: 'produits',
        loadChildren: () => import('./features/produits/produits.module').then(m => m.ProduitsModule)
      },
      {
        path: 'categories',
        loadChildren: () => import('./features/categories/categories.module').then(m => m.CategoriesModule)
      },
      {
        path: 'stocks',
        loadChildren: () => import('./features/stocks/stocks.module').then(m => m.StocksModule)
      },
      {
        path: 'entrepots',
        loadChildren: () => import('./features/entrepots/entrepots.module').then(m => m.EntrepotsModule)
      },
      {
        path: 'clients',
        loadChildren: () => import('./features/clients/clients.module').then(m => m.ClientsModule)
      },
      {
        path: 'fournisseurs',
        loadChildren: () => import('./features/fournisseurs/fournisseurs.module').then(m => m.FournisseursModule)
      },
      {
        path: 'ventes',
        loadChildren: () => import('./features/ventes/ventes.module').then(m => m.VentesModule)
      },
      {
        path: 'achats',
        loadChildren: () => import('./features/achats/achats.module').then(m => m.AchatsModule)
      },
      {
        path: 'finances',
        loadChildren: () => import('./features/finances/finances.module').then(m => m.FinancesModule)
      },
      {
        path: 'rapports',
        loadChildren: () => import('./features/rapports/rapports.module').then(m => m.RapportsModule)
      },
      {
        path: 'utilisateurs',
        loadChildren: () => import('./features/utilisateurs/utilisateurs.module').then(m => m.UtilisateursModule)
      },
      {
        path: 'parametres',
        loadChildren: () => import('./features/parametres/parametres.module').then(m => m.ParametresModule)
      }
    ]
  },
  { path: '**', redirectTo: 'dashboard' }
];

@NgModule({
  imports: [RouterModule.forRoot(routes)],
  exports: [RouterModule]
})
export class AppRoutingModule { }
