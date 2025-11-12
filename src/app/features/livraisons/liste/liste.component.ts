import { Component, OnInit } from '@angular/core';
import { Router } from '@angular/router';
import { MatDialog } from '@angular/material/dialog';
import { ToastrService } from 'ngx-toastr';
import { LivraisonsService } from '../../../core/services/livraisons.service';
import { Livraison } from '../../../core/models';
import { ConfirmDialogComponent } from '../../../shared/components/confirm-dialog/confirm-dialog.component';

@Component({
  selector: 'app-liste',
  templateUrl: './liste.component.html',
  styleUrl: './liste.component.scss'
})
export class ListeComponent implements OnInit {
  displayedColumns: string[] = ['vente', 'client', 'adresse_livraison', 'date_livraison_prevue', 'statut', 'actions'];
  livraisons: Livraison[] = [];
  loading = false;
  searchTerm = '';
  totalItems = 0;
  pageSize = 10;
  currentPage = 1;

  constructor(
    private livraisonsService: LivraisonsService,
    private router: Router,
    private dialog: MatDialog,
    private toastr: ToastrService
  ) {}

  ngOnInit(): void {
    this.loadLivraisons();
  }

  loadLivraisons(): void {
    this.loading = true;
    const params = {
      page: this.currentPage,
      per_page: this.pageSize,
      search: this.searchTerm || undefined
    };

    this.livraisonsService.getAll(params).subscribe({
      next: (response) => {
        this.livraisons = response.data;
        this.totalItems = response.total;
        this.loading = false;
      },
      error: (err) => {
        this.toastr.error('Erreur lors du chargement des livraisons', 'Erreur');
        this.loading = false;
      }
    });
  }

  onSearch(): void {
    this.currentPage = 1;
    this.loadLivraisons();
  }

  onPageChange(event: any): void {
    this.currentPage = event.pageIndex + 1;
    this.pageSize = event.pageSize;
    this.loadLivraisons();
  }

  onEdit(livraison: Livraison): void {
    this.router.navigate(['/livraisons/edit', livraison.id]);
  }

  onDelete(livraison: Livraison): void {
    const dialogRef = this.dialog.open(ConfirmDialogComponent, {
      width: '400px',
      data: {
        title: 'Confirmer la suppression',
        message: `Êtes-vous sûr de vouloir supprimer cette livraison ?`,
        confirmText: 'Supprimer',
        cancelText: 'Annuler'
      }
    });

    dialogRef.afterClosed().subscribe(result => {
      if (result) {
        this.livraisonsService.delete(livraison.id).subscribe({
          next: () => {
            this.toastr.success('Livraison supprimée avec succès');
            this.loadLivraisons();
          },
          error: (err) => {
            this.toastr.error('Erreur lors de la suppression', 'Erreur');
          }
        });
      }
    });
  }

  onCreate(): void {
    this.router.navigate(['/livraisons/nouvelle']);
  }

  onDemarrer(livraison: Livraison): void {
    const dialogRef = this.dialog.open(ConfirmDialogComponent, {
      width: '400px',
      data: {
        title: 'Démarrer la livraison',
        message: `Confirmez-vous le démarrage de cette livraison ?`,
        confirmText: 'Démarrer',
        cancelText: 'Annuler'
      }
    });

    dialogRef.afterClosed().subscribe(result => {
      if (result) {
        this.livraisonsService.demarrer(livraison.id).subscribe({
          next: () => {
            this.toastr.success('Livraison démarrée avec succès');
            this.loadLivraisons();
          },
          error: (err) => {
            this.toastr.error('Erreur lors du démarrage', 'Erreur');
          }
        });
      }
    });
  }

  onConfirmer(livraison: Livraison): void {
    const dialogRef = this.dialog.open(ConfirmDialogComponent, {
      width: '400px',
      data: {
        title: 'Confirmer la livraison',
        message: `Confirmez-vous que cette livraison a été effectuée ?`,
        confirmText: 'Confirmer',
        cancelText: 'Annuler'
      }
    });

    dialogRef.afterClosed().subscribe(result => {
      if (result) {
        this.livraisonsService.confirmer(livraison.id).subscribe({
          next: () => {
            this.toastr.success('Livraison confirmée avec succès');
            this.loadLivraisons();
          },
          error: (err) => {
            this.toastr.error('Erreur lors de la confirmation', 'Erreur');
          }
        });
      }
    });
  }

  onAnnuler(livraison: Livraison): void {
    const dialogRef = this.dialog.open(ConfirmDialogComponent, {
      width: '400px',
      data: {
        title: 'Annuler la livraison',
        message: `Êtes-vous sûr de vouloir annuler cette livraison ?`,
        confirmText: 'Annuler la livraison',
        cancelText: 'Retour'
      }
    });

    dialogRef.afterClosed().subscribe(result => {
      if (result) {
        const motif = prompt('Motif d\'annulation :');
        if (motif) {
          this.livraisonsService.annuler(livraison.id, motif).subscribe({
            next: () => {
              this.toastr.success('Livraison annulée avec succès');
              this.loadLivraisons();
            },
            error: (err) => {
              this.toastr.error('Erreur lors de l\'annulation', 'Erreur');
            }
          });
        }
      }
    });
  }

  onBonLivraison(livraison: Livraison): void {
    this.livraisonsService.getBonLivraison(livraison.id).subscribe({
      next: (blob) => {
        const url = window.URL.createObjectURL(blob);
        const link = document.createElement('a');
        link.href = url;
        link.download = `bon-livraison-${livraison.id}.pdf`;
        link.click();
        window.URL.revokeObjectURL(url);
      },
      error: (err) => {
        this.toastr.error('Erreur lors du téléchargement du bon de livraison', 'Erreur');
      }
    });
  }

  getStatutColor(statut: string): string {
    switch (statut) {
      case 'En préparation': return 'accent';
      case 'En cours': return 'primary';
      case 'Livrée': return 'primary';
      case 'Annulée': return 'warn';
      default: return '';
    }
  }

  canDemarrer(livraison: Livraison): boolean {
    return livraison.statut === 'En préparation';
  }

  canConfirmer(livraison: Livraison): boolean {
    return livraison.statut === 'En cours';
  }

  canAnnuler(livraison: Livraison): boolean {
    return livraison.statut === 'En préparation' || livraison.statut === 'En cours';
  }

  canEdit(livraison: Livraison): boolean {
    return livraison.statut === 'En préparation';
  }

  canDelete(livraison: Livraison): boolean {
    return livraison.statut === 'En préparation';
  }
}
