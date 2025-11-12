import { Component, OnInit } from '@angular/core';
import { AchatsService } from '../../../core/services/achats.service';
import { Achat } from '../../../core/models';
import { ToastrService } from 'ngx-toastr';

@Component({
  selector: 'app-liste',
  templateUrl: './liste.component.html',
  styleUrl: './liste.component.scss'
})
export class ListeComponent implements OnInit {
  displayedColumns: string[] = ['numero', 'fournisseur', 'date_commande', 'montant_total', 'statut', 'actions'];
  achats: Achat[] = [];
  loading = false;

  constructor(
    private achatsService: AchatsService,
    private toastr: ToastrService
  ) {}

  ngOnInit(): void {
    this.loadAchats();
  }

  loadAchats(): void {
    this.loading = true;
    this.achatsService.getAll().subscribe({
      next: (response) => {
        this.achats = response.data;
        this.loading = false;
      },
      error: (err) => {
        this.toastr.error('Erreur lors du chargement des achats', 'Erreur');
        this.loading = false;
      }
    });
  }

  onValider(achat: Achat): void {
    if (confirm(`Confirmer la commande ${achat.numero} ?`)) {
      this.achatsService.valider(achat.id).subscribe({
        next: () => {
          this.toastr.success('Commande validée avec succès');
          this.loadAchats();
        },
        error: (err) => {
          this.toastr.error(err.message || 'Erreur lors de la validation', 'Erreur');
        }
      });
    }
  }

  onRecevoir(achat: Achat): void {
    if (confirm(`Marquer la commande ${achat.numero} comme reçue ?`)) {
      this.achatsService.recevoir(achat.id).subscribe({
        next: () => {
          this.toastr.success('Commande reçue avec succès');
          this.loadAchats();
        },
        error: (err) => {
          this.toastr.error(err.message || 'Erreur lors de la réception', 'Erreur');
        }
      });
    }
  }

  onAnnuler(achat: Achat): void {
    if (confirm(`Annuler la commande ${achat.numero} ?`)) {
      this.achatsService.annuler(achat.id).subscribe({
        next: () => {
          this.toastr.success('Commande annulée avec succès');
          this.loadAchats();
        },
        error: (err) => {
          this.toastr.error(err.message || 'Erreur lors de l\'annulation', 'Erreur');
        }
      });
    }
  }

  getStatutClass(statut: string): string {
    const statusClasses: { [key: string]: string } = {
      'brouillon': 'status-draft',
      'validee': 'status-validated',
      'recue': 'status-received',
      'annulee': 'status-cancelled'
    };
    return statusClasses[statut] || '';
  }
}
