import { Component, OnInit } from '@angular/core';
import { Router } from '@angular/router';
import { MatDialog } from '@angular/material/dialog';
import { ToastrService } from 'ngx-toastr';
import { PaiementsService } from '../../../core/services/paiements.service';
import { Paiement } from '../../../core/models';
import { ConfirmDialogComponent } from '../../../shared/components/confirm-dialog/confirm-dialog.component';

@Component({
  selector: 'app-liste',
  templateUrl: './liste.component.html',
  styleUrl: './liste.component.scss'
})
export class ListeComponent implements OnInit {
  displayedColumns: string[] = ['date_paiement', 'type', 'reference_facture', 'montant', 'mode_paiement', 'reference', 'actions'];
  paiements: Paiement[] = [];
  loading = false;
  searchTerm = '';
  totalItems = 0;
  pageSize = 10;
  currentPage = 1;

  constructor(
    private paiementsService: PaiementsService,
    private router: Router,
    private dialog: MatDialog,
    private toastr: ToastrService
  ) {}

  ngOnInit(): void {
    this.loadPaiements();
  }

  loadPaiements(): void {
    this.loading = true;
    const params = {
      page: this.currentPage,
      per_page: this.pageSize,
      search: this.searchTerm || undefined
    };

    this.paiementsService.getAll(params).subscribe({
      next: (response) => {
        this.paiements = response.data;
        this.totalItems = response.total;
        this.loading = false;
      },
      error: (err) => {
        this.toastr.error('Erreur lors du chargement des paiements', 'Erreur');
        this.loading = false;
      }
    });
  }

  onSearch(): void {
    this.currentPage = 1;
    this.loadPaiements();
  }

  onPageChange(event: any): void {
    this.currentPage = event.pageIndex + 1;
    this.pageSize = event.pageSize;
    this.loadPaiements();
  }

  onEdit(paiement: Paiement): void {
    this.router.navigate(['/paiements/edit', paiement.id]);
  }

  onDelete(paiement: Paiement): void {
    const dialogRef = this.dialog.open(ConfirmDialogComponent, {
      width: '400px',
      data: {
        title: 'Confirmer la suppression',
        message: `Êtes-vous sûr de vouloir supprimer ce paiement de ${paiement.montant} FCFA ?`,
        confirmText: 'Supprimer',
        cancelText: 'Annuler'
      }
    });

    dialogRef.afterClosed().subscribe(result => {
      if (result) {
        this.paiementsService.delete(paiement.id).subscribe({
          next: () => {
            this.toastr.success('Paiement supprimé avec succès');
            this.loadPaiements();
          },
          error: (err) => {
            this.toastr.error('Erreur lors de la suppression', 'Erreur');
          }
        });
      }
    });
  }

  onCreate(): void {
    this.router.navigate(['/paiements/nouveau']);
  }

  getFactureReference(paiement: Paiement): string {
    if (paiement.type === 'Client' && paiement.facture) {
      return paiement.facture.numero;
    } else if (paiement.type === 'Fournisseur' && paiement.facture_fournisseur) {
      return paiement.facture_fournisseur.numero_facture;
    }
    return '-';
  }

  getTypeColor(type: string): string {
    return type === 'Client' ? 'primary' : 'accent';
  }
}
