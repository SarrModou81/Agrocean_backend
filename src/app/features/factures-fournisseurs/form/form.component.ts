import { Component, OnInit } from '@angular/core';
import { FormBuilder, FormGroup, Validators } from '@angular/forms';
import { ActivatedRoute, Router } from '@angular/router';
import { ToastrService } from 'ngx-toastr';
import { FacturesService } from '../../../core/services/factures.service';
import { CommandesAchatService } from '../../../core/services/commandes-achat.service';
import { CommandeAchat } from '../../../core/models';

@Component({
  selector: 'app-form',
  templateUrl: './form.component.html',
  styleUrl: './form.component.scss'
})
export class FormComponent implements OnInit {
  factureForm!: FormGroup;
  loading = false;
  isEditMode = false;
  factureId?: number;

  commandes: CommandeAchat[] = [];

  statuts = [
    { value: 'Brouillon', label: 'Brouillon' },
    { value: 'Reçue', label: 'Reçue' },
    { value: 'Payée', label: 'Payée' },
    { value: 'Annulée', label: 'Annulée' }
  ];

  constructor(
    private fb: FormBuilder,
    private facturesService: FacturesService,
    private commandesAchatService: CommandesAchatService,
    private route: ActivatedRoute,
    private router: Router,
    private toastr: ToastrService
  ) {}

  ngOnInit(): void {
    this.factureForm = this.fb.group({
      commande_achat_id: ['', Validators.required],
      numero_facture: ['', Validators.required],
      date_emission: [new Date(), Validators.required],
      date_echeance: ['', Validators.required],
      statut: ['Brouillon', Validators.required],
      notes: ['']
    });

    this.loadCommandes();

    this.route.params.subscribe(params => {
      if (params['id']) {
        this.isEditMode = true;
        this.factureId = +params['id'];
        this.loadFacture();
      }
    });
  }

  loadCommandes(): void {
    this.commandesAchatService.getAll({ per_page: 1000 }).subscribe({
      next: (response) => {
        this.commandes = response.data;
      },
      error: (err) => {
        this.toastr.error('Erreur lors du chargement des commandes', 'Erreur');
      }
    });
  }

  loadFacture(): void {
    if (!this.factureId) return;

    this.loading = true;
    this.facturesService.getFournisseurById(this.factureId).subscribe({
      next: (response) => {
        const facture = response.data;
        this.factureForm.patchValue({
          commande_achat_id: facture.commande_achat_id,
          numero_facture: facture.numero_facture,
          date_emission: new Date(facture.date_emission),
          date_echeance: facture.date_echeance ? new Date(facture.date_echeance) : undefined,
          statut: facture.statut,
          notes: facture.notes
        });
        this.loading = false;
      },
      error: (err) => {
        this.toastr.error('Erreur lors du chargement', 'Erreur');
        this.loading = false;
        this.router.navigate(['/factures-fournisseurs']);
      }
    });
  }

  onSubmit(): void {
    if (this.factureForm.invalid) {
      this.factureForm.markAllAsTouched();
      return;
    }

    this.loading = true;
    const formData = this.factureForm.value;

    const request = this.isEditMode && this.factureId
      ? this.facturesService.updateFournisseur(this.factureId, formData)
      : this.facturesService.createFournisseur(formData);

    request.subscribe({
      next: () => {
        this.toastr.success(
          this.isEditMode ? 'Facture modifiée avec succès' : 'Facture créée avec succès',
          'Succès'
        );
        this.router.navigate(['/factures-fournisseurs']);
      },
      error: (err) => {
        this.toastr.error('Erreur lors de l\'enregistrement', 'Erreur');
        this.loading = false;
      }
    });
  }

  onCancel(): void {
    this.router.navigate(['/factures-fournisseurs']);
  }
}
