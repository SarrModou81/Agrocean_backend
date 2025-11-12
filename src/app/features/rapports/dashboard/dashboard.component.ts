import { Component, OnInit } from '@angular/core';

@Component({
  selector: 'app-dashboard',
  templateUrl: './dashboard.component.html',
  styleUrl: './dashboard.component.scss'
})
export class DashboardComponent implements OnInit {
  loading = false;
  selectedPeriod = 'month';

  reportTypes = [
    {
      title: 'Rapport des Ventes',
      description: 'Analyse détaillée des ventes par période',
      icon: 'shopping_cart',
      color: '#3498db'
    },
    {
      title: 'Rapport des Stocks',
      description: 'État des stocks et mouvements',
      icon: 'inventory',
      color: '#27ae60'
    },
    {
      title: 'Rapport Financier',
      description: 'Synthèse des revenus et dépenses',
      icon: 'account_balance',
      color: '#9b59b6'
    },
    {
      title: 'Rapport des Clients',
      description: 'Statistiques et activités clients',
      icon: 'people',
      color: '#e67e22'
    },
    {
      title: 'Rapport des Fournisseurs',
      description: 'Achats et relations fournisseurs',
      icon: 'business',
      color: '#34495e'
    },
    {
      title: 'Rapport d\'Activité',
      description: 'Vue globale de l\'activité',
      icon: 'assessment',
      color: '#e74c3c'
    }
  ];

  constructor() {}

  ngOnInit(): void {
    // Load reports data
  }

  generateReport(reportType: any): void {
    console.log('Generating report:', reportType.title);
  }
}
