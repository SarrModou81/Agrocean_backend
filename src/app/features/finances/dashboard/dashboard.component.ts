import { Component, OnInit } from '@angular/core';

@Component({
  selector: 'app-dashboard',
  templateUrl: './dashboard.component.html',
  styleUrl: './dashboard.component.scss'
})
export class DashboardComponent implements OnInit {
  loading = false;

  stats = [
    {
      title: 'Chiffre d\'Affaires',
      value: '12 500 000 FCFA',
      icon: 'attach_money',
      color: '#27ae60',
      change: '+12%'
    },
    {
      title: 'Dépenses',
      value: '5 200 000 FCFA',
      icon: 'money_off',
      color: '#e74c3c',
      change: '-5%'
    },
    {
      title: 'Bénéfices',
      value: '7 300 000 FCFA',
      icon: 'trending_up',
      color: '#3498db',
      change: '+18%'
    },
    {
      title: 'En Attente',
      value: '1 800 000 FCFA',
      icon: 'hourglass_empty',
      color: '#f39c12',
      change: '8 factures'
    }
  ];

  constructor() {}

  ngOnInit(): void {
    // Load financial data
  }
}
