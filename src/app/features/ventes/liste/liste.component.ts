import { Component, OnInit } from '@angular/core';
import { VentesService } from '../../../core/services/ventes.service';
import { Vente } from '../../../core/models';
import { ToastrService } from 'ngx-toastr';

@Component({
  selector: 'app-liste',
  templateUrl: './liste.component.html',
  styleUrl: './liste.component.scss'
})
export class ListeComponent implements OnInit {
  displayedColumns: string[] = ['numero', 'client', 'date_vente', 'montant_total', 'statut', 'actions'];
  ventes: Vente[] = [];
  loading = false;

  constructor(
    private ventesService: VentesService,
    private toastr: ToastrService
  ) {}

  ngOnInit(): void {
    this.loadVentes();
  }

  loadVentes(): void {
    this.loading = true;
    this.ventesService.getAll().subscribe({
      next: (response) => {
        this.ventes = response.data;
        this.loading = false;
      },
      error: (err) => {
        this.toastr.error('Erreur lors du chargement des ventes', 'Erreur');
        this.loading = false;
      }
    });
  }

  onValider(vente: Vente): void {
    if (confirm(`Confirmer la vente ${vente.numero} ?`)) {
      this.ventesService.valider(vente.id).subscribe({
        next: () => {
          this.toastr.success('Vente validée avec succès');
          this.loadVentes();
        },
        error: (err) => {
          this.toastr.error(err.message || 'Erreur lors de la validation', 'Erreur');
        }
      });
    }
  }

  onAnnuler(vente: Vente): void {
    if (confirm(`Annuler la vente ${vente.numero} ?`)) {
      this.ventesService.annuler(vente.id).subscribe({
        next: () => {
          this.toastr.success('Vente annulée avec succès');
          this.loadVentes();
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
      'livree': 'status-delivered',
      'annulee': 'status-cancelled'
    };
    return statusClasses[statut] || '';
  }
}
