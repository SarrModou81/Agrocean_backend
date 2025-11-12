import { Component, OnInit } from '@angular/core';
import { MatDialog } from '@angular/material/dialog';
import { AchatsService } from '../../../core/services/achats.service';
import { Fournisseur } from '../../../core/models';
import { ToastrService } from 'ngx-toastr';
import { ConfirmDialogComponent } from '../../../shared/components/confirm-dialog/confirm-dialog.component';

@Component({
  selector: 'app-liste',
  templateUrl: './liste.component.html',
  styleUrl: './liste.component.scss'
})
export class ListeComponent implements OnInit {
  displayedColumns: string[] = ['nom', 'email', 'telephone', 'adresse', 'actions'];
  fournisseurs: Fournisseur[] = [];
  loading = false;

  constructor(
    private achatsService: AchatsService,
    private toastr: ToastrService,
    private dialog: MatDialog
  ) {}

  ngOnInit(): void {
    this.loadFournisseurs();
  }

  loadFournisseurs(): void {
    this.loading = true;
    this.achatsService.getAllFournisseurs().subscribe({
      next: (response) => {
        this.fournisseurs = response.data;
        this.loading = false;
      },
      error: (err) => {
        this.toastr.error('Erreur lors du chargement', 'Erreur');
        this.loading = false;
      }
    });
  }

  onDelete(fournisseur: Fournisseur): void {
    const dialogRef = this.dialog.open(ConfirmDialogComponent, {
      width: '400px',
      data: {
        title: 'Confirmer la suppression',
        message: `Êtes-vous sûr de vouloir supprimer le fournisseur "${fournisseur.nom}" ?`,
        confirmText: 'Supprimer',
        cancelText: 'Annuler'
      }
    });

    dialogRef.afterClosed().subscribe(result => {
      if (result) {
        this.achatsService.deleteFournisseur(fournisseur.id).subscribe({
          next: () => {
            this.toastr.success('Fournisseur supprimé avec succès');
            this.loadFournisseurs();
          },
          error: (err) => {
            this.toastr.error(err.message || 'Erreur lors de la suppression', 'Erreur');
          }
        });
      }
    });
  }
}
