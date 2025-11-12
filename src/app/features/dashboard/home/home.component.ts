import { Component, OnInit } from '@angular/core';
import { Router } from '@angular/router';
import { AuthService } from '../../../core/services/auth.service';
import { User } from '../../../core/models/user.model';

@Component({
  selector: 'app-home',
  templateUrl: './home.component.html',
  styleUrl: './home.component.scss'
})
export class HomeComponent implements OnInit {
  currentUser: User | null = null;
  currentDate = new Date();

  stats = [
    {
      title: 'Produits en Stock',
      value: '156',
      subValue: '+12 ce mois',
      icon: 'inventory_2',
      gradient: 'linear-gradient(135deg, #1565C0, #42a5f5)',
      route: '/produits'
    },
    {
      title: 'Ventes du Mois',
      value: '45',
      subValue: '2.5M FCFA',
      icon: 'shopping_cart',
      gradient: 'linear-gradient(135deg, #43A047, #66bb6a)',
      route: '/ventes'
    },
    {
      title: 'Commandes en Cours',
      value: '8',
      subValue: '3 à livrer',
      icon: 'local_shipping',
      gradient: 'linear-gradient(135deg, #FB8C00, #ffa726)',
      route: '/achats'
    },
    {
      title: 'Stock Faible',
      value: '5',
      subValue: 'Nécessite attention',
      icon: 'warning',
      gradient: 'linear-gradient(135deg, #e74c3c, #f39c12)',
      route: '/stocks'
    }
  ];

  quickActions = [
    { title: 'Nouvelle Vente', icon: 'add_shopping_cart', route: '/ventes/nouvelle', color: 'primary' },
    { title: 'Nouvelle Commande', icon: 'add_box', route: '/achats/nouvelle', color: 'accent' },
    { title: 'Ajuster Stock', icon: 'inventory', route: '/stocks', color: 'primary' },
    { title: 'Nouveau Client', icon: 'person_add', route: '/clients/nouveau', color: 'accent' }
  ];

  modules = [
    { title: 'Produits', icon: 'inventory_2', route: '/produits', description: 'Gérer le catalogue de produits' },
    { title: 'Stocks', icon: 'warehouse', route: '/stocks', description: 'Gérer les stocks et entrepôts' },
    { title: 'Ventes', icon: 'shopping_cart', route: '/ventes', description: 'Gérer les ventes et factures' },
    { title: 'Achats', icon: 'local_shipping', route: '/achats', description: 'Gérer les commandes d\'achat' },
    { title: 'Clients', icon: 'people', route: '/clients', description: 'Gérer les clients' },
    { title: 'Fournisseurs', icon: 'business', route: '/fournisseurs', description: 'Gérer les fournisseurs' }
  ];

  constructor(
    private authService: AuthService,
    private router: Router
  ) {}

  ngOnInit(): void {
    this.authService.currentUser$.subscribe(user => {
      this.currentUser = user;
    });
  }

  logout(): void {
    this.authService.logout().subscribe(() => {
      this.router.navigate(['/auth/login']);
    });
  }

  getGreeting(): string {
    const hour = this.currentDate.getHours();
    if (hour < 12) return 'Bonjour';
    if (hour < 18) return 'Bon après-midi';
    return 'Bonsoir';
  }
}
