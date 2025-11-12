import { Component, OnInit } from '@angular/core';
import { StocksService } from '../../../core/services/stocks.service';
import { Stock } from '../../../core/models';
import { ToastrService } from 'ngx-toastr';

@Component({
  selector: 'app-liste',
  templateUrl: './liste.component.html',
  styleUrl: './liste.component.scss'
})
export class ListeComponent implements OnInit {
  displayedColumns: string[] = ['produit', 'entrepot', 'quantite', 'numero_lot', 'statut'];
  stocks: Stock[] = [];
  loading = false;

  constructor(
    private stocksService: StocksService,
    private toastr: ToastrService
  ) {}

  ngOnInit(): void {
    this.loadStocks();
  }

  loadStocks(): void {
    this.loading = true;
    this.stocksService.getAll().subscribe({
      next: (response) => {
        this.stocks = response.data;
        this.loading = false;
      },
      error: (err) => {
        this.toastr.error('Erreur lors du chargement des stocks', 'Erreur');
        this.loading = false;
      }
    });
  }
}
