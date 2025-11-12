import { Component, OnInit } from '@angular/core';
import { MatDialog } from '@angular/material/dialog';
import { StocksService } from '../../../core/services/stocks.service';
import { Stock } from '../../../core/models';
import { ToastrService } from 'ngx-toastr';
import { StockAdjustDialogComponent } from '../../../shared/components/stock-adjust-dialog/stock-adjust-dialog.component';

@Component({
  selector: 'app-liste',
  templateUrl: './liste.component.html',
  styleUrl: './liste.component.scss'
})
export class ListeComponent implements OnInit {
  displayedColumns: string[] = ['produit', 'entrepot', 'quantite', 'numero_lot', 'statut', 'actions'];
  stocks: Stock[] = [];
  loading = false;

  constructor(
    private stocksService: StocksService,
    private toastr: ToastrService,
    private dialog: MatDialog
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

  onAdjustStock(stock: Stock): void {
    const dialogRef = this.dialog.open(StockAdjustDialogComponent, {
      width: '500px',
      data: {
        stockId: stock.id,
        produitNom: stock.produit?.nom || 'N/A',
        quantiteActuelle: stock.quantite
      }
    });

    dialogRef.afterClosed().subscribe(result => {
      if (result) {
        const adjustData = {
          stock_id: stock.id,
          type_mouvement: result.type_mouvement,
          quantite: result.quantite,
          motif: result.motif
        };

        this.stocksService.ajuster(adjustData).subscribe({
          next: () => {
            this.toastr.success('Stock ajusté avec succès');
            this.loadStocks();
          },
          error: (err) => {
            this.toastr.error(err.message || 'Erreur lors de l\'ajustement', 'Erreur');
          }
        });
      }
    });
  }
}
