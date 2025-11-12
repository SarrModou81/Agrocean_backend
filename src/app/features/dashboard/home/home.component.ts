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
  stats = [
    { title: 'Produits en Stock', value: '0', icon: 'inventory_2', color: '#3498db' },
    { title: 'Ventes du Mois', value: '0', icon: 'shopping_cart', color: '#27ae60' },
    { title: 'Commandes en Cours', value: '0', icon: 'local_shipping', color: '#f39c12' },
    { title: 'Alertes Stock', value: '0', icon: 'warning', color: '#e74c3c' }
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
}
