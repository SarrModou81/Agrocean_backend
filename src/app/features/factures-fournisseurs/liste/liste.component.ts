import { Component, OnInit } from '@angular/core';
import { Router } from '@angular/router';
import { MatDialog } from '@angular/material/dialog';
import { ToastrService } from 'ngx-toastr';
import { FacturesService } from '../../../core/services/factures.service';
import { FactureFournisseur } from '../../../core/models';
import { ConfirmDialogComponent } from '../../../shared/components/confirm-dialog/confirm-dialog.component';

@Component({
  selector: 'app-liste',
  templateUrl: './liste.component.html',
  styleUrl: './liste.component.scss'
})
export class ListeComponent implements OnInit {
  displayedColumns: string[] = ['numero_facture', 'fournisseur', 'date_emission', 'date_echeance', 'montant_total', 'statut', 'actions'];
  factures: FactureFournisseur[] = [];
  loading = false;
  searchTerm = '';
  totalItems = 0;
  pageSize = 10;
  currentPage = 1;

  constructor(
    private facturesService: FacturesService,
    private router: Router,
    private dialog: MatDialog,
    private toastr: ToastrService
  ) {}

  ngOnInit(): void {
    this.loadFactures();
  }

  loadFactures(): void {
    this.loading = true;
    const params = {
      page: this.currentPage,
      per_page: this.pageSize,
      search: this.searchTerm || undefined
    };

    this.facturesService.getFournisseurs(params).subscribe({
      next: (response: any) => {
        this.factures = response.data;
        this.totalItems = response.total;
        this.loading = false;
      },
      error: (err: any) => {
        this.toastr.error('Erreur lors du chargement des factures', 'Erreur');
        this.loading = false;
      }
    });
  }

  onSearch(): void {
    this.currentPage = 1;
    this.loadFactures();
  }

  onPageChange(event: any): void {
    this.currentPage = event.pageIndex + 1;
    this.pageSize = event.pageSize;
    this.loadFactures();
  }

  onEdit(facture: FactureFournisseur): void {
    this.router.navigate(['/factures-fournisseurs/edit', facture.id]);
  }

  onDelete(facture: FactureFournisseur): void {
    const dialogRef = this.dialog.open(ConfirmDialogComponent, {
      width: '400px',
      data: {
        title: 'Confirmer la suppression',
        message: `Êtes-vous sûr de vouloir supprimer la facture "${facture.numero_facture}" ?`,
        confirmText: 'Supprimer',
        cancelText: 'Annuler'
      }
    });

    dialogRef.afterClosed().subscribe(result => {
      if (result) {
        this.facturesService.deleteFournisseur(facture.id).subscribe({
          next: () => {
            this.toastr.success('Facture supprimée avec succès');
            this.loadFactures();
          },
          error: (err) => {
            this.toastr.error('Erreur lors de la suppression', 'Erreur');
          }
        });
      }
    });
  }

  onCreate(): void {
    this.router.navigate(['/factures-fournisseurs/nouvelle']);
  }

  onDownloadPDF(facture: FactureFournisseur): void {
    this.facturesService.getFournisseurPDF(facture.id).subscribe({
      next: (blob) => {
        const url = window.URL.createObjectURL(blob);
        const link = document.createElement('a');
        link.href = url;
        link.download = `facture-fournisseur-${facture.numero_facture}.pdf`;
        link.click();
        window.URL.revokeObjectURL(url);
      },
      error: (err) => {
        this.toastr.error('Erreur lors du téléchargement du PDF', 'Erreur');
      }
    });
  }

  getStatutColor(statut: string): string {
    switch (statut) {
      case 'Brouillon': return 'accent';
      case 'Reçue': return 'primary';
      case 'Payée': return 'primary';
      case 'Annulée': return 'warn';
      default: return '';
    }
  }

  canEdit(facture: FactureFournisseur): boolean {
    return facture.statut === 'Brouillon';
  }

  canDelete(facture: FactureFournisseur): boolean {
    return facture.statut === 'Brouillon';
  }

  isOverdue(facture: FactureFournisseur): boolean {
    if (facture.statut === 'Payée' || facture.statut === 'Annulée') {
      return false;
    }
    if (!facture.date_echeance) return false;
    const echeance = new Date(facture.date_echeance);
    const today = new Date();
    return echeance < today;
  }
}
