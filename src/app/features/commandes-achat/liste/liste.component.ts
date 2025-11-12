import { Component, OnInit } from '@angular/core';
import { Router } from '@angular/router';
import { MatDialog } from '@angular/material/dialog';
import { ToastrService } from 'ngx-toastr';
import { CommandesAchatService } from '../../../core/services/commandes-achat.service';
import { CommandeAchat } from '../../../core/models';
import { ConfirmDialogComponent } from '../../../shared/components/confirm-dialog/confirm-dialog.component';

@Component({
  selector: 'app-liste',
  templateUrl: './liste.component.html',
  styleUrl: './liste.component.scss'
})
export class ListeComponent implements OnInit {
  displayedColumns: string[] = ['numero', 'fournisseur', 'date_commande', 'date_livraison_prevue', 'montant_total', 'statut', 'actions'];
  commandes: CommandeAchat[] = [];
  loading = false;
  searchTerm = '';
  totalItems = 0;
  pageSize = 10;
  currentPage = 1;

  constructor(
    private commandesAchatService: CommandesAchatService,
    private router: Router,
    private dialog: MatDialog,
    private toastr: ToastrService
  ) {}

  ngOnInit(): void {
    this.loadCommandes();
  }

  loadCommandes(): void {
    this.loading = true;
    const params = {
      page: this.currentPage,
      per_page: this.pageSize,
      search: this.searchTerm || undefined
    };

    this.commandesAchatService.getAll(params).subscribe({
      next: (response) => {
        this.commandes = response.data;
        this.totalItems = response.total;
        this.loading = false;
      },
      error: (err) => {
        this.toastr.error('Erreur lors du chargement des commandes', 'Erreur');
        this.loading = false;
      }
    });
  }

  onSearch(): void {
    this.currentPage = 1;
    this.loadCommandes();
  }

  onPageChange(event: any): void {
    this.currentPage = event.pageIndex + 1;
    this.pageSize = event.pageSize;
    this.loadCommandes();
  }

  onEdit(commande: CommandeAchat): void {
    this.router.navigate(['/commandes-achat/edit', commande.id]);
  }

  onDelete(commande: CommandeAchat): void {
    const dialogRef = this.dialog.open(ConfirmDialogComponent, {
      width: '400px',
      data: {
        title: 'Confirmer la suppression',
        message: `Êtes-vous sûr de vouloir supprimer la commande "${commande.numero}" ?`,
        confirmText: 'Supprimer',
        cancelText: 'Annuler'
      }
    });

    dialogRef.afterClosed().subscribe(result => {
      if (result) {
        this.commandesAchatService.delete(commande.id).subscribe({
          next: () => {
            this.toastr.success('Commande supprimée avec succès');
            this.loadCommandes();
          },
          error: (err) => {
            this.toastr.error('Erreur lors de la suppression', 'Erreur');
          }
        });
      }
    });
  }

  onCreate(): void {
    this.router.navigate(['/commandes-achat/nouvelle']);
  }

  onValider(commande: CommandeAchat): void {
    const dialogRef = this.dialog.open(ConfirmDialogComponent, {
      width: '400px',
      data: {
        title: 'Valider la commande',
        message: `Êtes-vous sûr de vouloir valider la commande "${commande.numero}" ?`,
        confirmText: 'Valider',
        cancelText: 'Annuler'
      }
    });

    dialogRef.afterClosed().subscribe(result => {
      if (result) {
        this.commandesAchatService.valider(commande.id).subscribe({
          next: () => {
            this.toastr.success('Commande validée avec succès');
            this.loadCommandes();
          },
          error: (err) => {
            this.toastr.error('Erreur lors de la validation', 'Erreur');
          }
        });
      }
    });
  }

  onReceptionner(commande: CommandeAchat): void {
    const dialogRef = this.dialog.open(ConfirmDialogComponent, {
      width: '400px',
      data: {
        title: 'Réceptionner la commande',
        message: `Confirmez-vous la réception de la commande "${commande.numero}" ? Les stocks seront mis à jour.`,
        confirmText: 'Réceptionner',
        cancelText: 'Annuler'
      }
    });

    dialogRef.afterClosed().subscribe(result => {
      if (result) {
        this.commandesAchatService.receptionner(commande.id).subscribe({
          next: () => {
            this.toastr.success('Commande réceptionnée avec succès');
            this.loadCommandes();
          },
          error: (err) => {
            this.toastr.error('Erreur lors de la réception', 'Erreur');
          }
        });
      }
    });
  }

  onAnnuler(commande: CommandeAchat): void {
    const dialogRef = this.dialog.open(ConfirmDialogComponent, {
      width: '400px',
      data: {
        title: 'Annuler la commande',
        message: `Êtes-vous sûr de vouloir annuler la commande "${commande.numero}" ?`,
        confirmText: 'Annuler la commande',
        cancelText: 'Retour'
      }
    });

    dialogRef.afterClosed().subscribe(result => {
      if (result) {
        const motif = prompt('Motif d\'annulation :');
        if (motif) {
          this.commandesAchatService.annuler(commande.id, motif).subscribe({
            next: () => {
              this.toastr.success('Commande annulée avec succès');
              this.loadCommandes();
            },
            error: (err) => {
              this.toastr.error('Erreur lors de l\'annulation', 'Erreur');
            }
          });
        }
      }
    });
  }

  getStatutColor(statut: string): string {
    switch (statut) {
      case 'Brouillon': return 'accent';
      case 'Validée': return 'primary';
      case 'Reçue': return 'primary';
      case 'Annulée': return 'warn';
      default: return '';
    }
  }

  canValider(commande: CommandeAchat): boolean {
    return commande.statut === 'Brouillon';
  }

  canReceptionner(commande: CommandeAchat): boolean {
    return commande.statut === 'Validée';
  }

  canAnnuler(commande: CommandeAchat): boolean {
    return commande.statut === 'Brouillon' || commande.statut === 'Validée';
  }

  canEdit(commande: CommandeAchat): boolean {
    return commande.statut === 'Brouillon';
  }

  canDelete(commande: CommandeAchat): boolean {
    return commande.statut === 'Brouillon';
  }
}
