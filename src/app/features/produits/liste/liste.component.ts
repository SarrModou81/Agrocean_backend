import { Component, OnInit, ViewChild } from '@angular/core';
import { Router } from '@angular/router';
import { MatPaginator } from '@angular/material/paginator';
import { MatSort } from '@angular/material/sort';
import { MatDialog } from '@angular/material/dialog';
import { ToastrService } from 'ngx-toastr';
import { ProduitsService } from '../../../core/services/produits.service';
import { Produit } from '../../../core/models';
import { ConfirmDialogComponent } from '../../../shared/components/confirm-dialog/confirm-dialog.component';

@Component({
  selector: 'app-liste',
  templateUrl: './liste.component.html',
  styleUrl: './liste.component.scss'
})
export class ListeComponent implements OnInit {
  @ViewChild(MatPaginator) paginator!: MatPaginator;
  @ViewChild(MatSort) sort!: MatSort;

  displayedColumns: string[] = ['reference', 'nom', 'categorie', 'prix_vente', 'stock_total', 'actions'];
  produits: Produit[] = [];
  loading = false;
  searchTerm = '';
  totalItems = 0;
  pageSize = 10;
  currentPage = 1;

  constructor(
    private produitsService: ProduitsService,
    private router: Router,
    private dialog: MatDialog,
    private toastr: ToastrService
  ) {}

  ngOnInit(): void {
    this.loadProduits();
  }

  loadProduits(): void {
    this.loading = true;
    const params = {
      page: this.currentPage,
      per_page: this.pageSize,
      search: this.searchTerm || undefined
    };

    this.produitsService.getAll(params).subscribe({
      next: (response) => {
        this.produits = response.data;
        this.totalItems = response.total;
        this.loading = false;
      },
      error: (err) => {
        this.toastr.error(err.message || 'Erreur lors du chargement', 'Erreur');
        this.loading = false;
      }
    });
  }

  onSearch(): void {
    this.currentPage = 1;
    this.loadProduits();
  }

  onPageChange(event: any): void {
    this.currentPage = event.pageIndex + 1;
    this.pageSize = event.pageSize;
    this.loadProduits();
  }

  onEdit(produit: Produit): void {
    this.router.navigate(['/produits/edit', produit.id]);
  }

  onDelete(produit: Produit): void {
    const dialogRef = this.dialog.open(ConfirmDialogComponent, {
      width: '400px',
      data: {
        title: 'Confirmer la suppression',
        message: `Êtes-vous sûr de vouloir supprimer le produit "${produit.nom}" ?`,
        confirmText: 'Supprimer',
        cancelText: 'Annuler'
      }
    });

    dialogRef.afterClosed().subscribe(result => {
      if (result) {
        this.produitsService.delete(produit.id).subscribe({
          next: () => {
            this.toastr.success('Produit supprimé avec succès');
            this.loadProduits();
          },
          error: (err) => {
            this.toastr.error(err.message || 'Erreur lors de la suppression', 'Erreur');
          }
        });
      }
    });
  }

  onCreate(): void {
    this.router.navigate(['/produits/nouveau']);
  }
}
