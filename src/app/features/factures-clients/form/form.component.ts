import { Component, OnInit } from '@angular/core';
import { FormBuilder, FormGroup, Validators } from '@angular/forms';
import { ActivatedRoute, Router } from '@angular/router';
import { ToastrService } from 'ngx-toastr';
import { FacturesService } from '../../../core/services/factures.service';
import { VentesService } from '../../../core/services/ventes.service';
import { Vente } from '../../../core/models';

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

  ventes: Vente[] = [];

  statuts = [
    { value: 'Brouillon', label: 'Brouillon' },
    { value: 'Envoyée', label: 'Envoyée' },
    { value: 'Payée', label: 'Payée' },
    { value: 'Annulée', label: 'Annulée' }
  ];

  constructor(
    private fb: FormBuilder,
    private facturesService: FacturesService,
    private ventesService: VentesService,
    private route: ActivatedRoute,
    private router: Router,
    private toastr: ToastrService
  ) {}

  ngOnInit(): void {
    this.factureForm = this.fb.group({
      vente_id: ['', Validators.required],
      date_emission: [new Date(), Validators.required],
      date_echeance: ['', Validators.required],
      statut: ['Brouillon', Validators.required],
      remise: [0, [Validators.min(0)]],
      notes: ['']
    });

    this.loadVentes();

    this.route.params.subscribe(params => {
      if (params['id']) {
        this.isEditMode = true;
        this.factureId = +params['id'];
        this.loadFacture();
      }
    });
  }

  loadVentes(): void {
    this.ventesService.getAll({ per_page: 1000 }).subscribe({
      next: (response) => {
        this.ventes = response.data;
      },
      error: (err) => {
        this.toastr.error('Erreur lors du chargement des ventes', 'Erreur');
      }
    });
  }

  loadFacture(): void {
    if (!this.factureId) return;

    this.loading = true;
    this.facturesService.getById(this.factureId).subscribe({
      next: (response) => {
        const facture = response.data;
        this.factureForm.patchValue({
          vente_id: facture.vente_id,
          date_emission: new Date(facture.date_emission),
          date_echeance: facture.date_echeance ? new Date(facture.date_echeance) : undefined,
          statut: facture.statut,
          remise: facture.remise || 0,
          notes: facture.notes
        });
        this.loading = false;
      },
      error: (err) => {
        this.toastr.error('Erreur lors du chargement', 'Erreur');
        this.loading = false;
        this.router.navigate(['/factures-clients']);
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
      ? this.facturesService.update(this.factureId, formData)
      : this.facturesService.create(formData);

    request.subscribe({
      next: () => {
        this.toastr.success(
          this.isEditMode ? 'Facture modifiée avec succès' : 'Facture créée avec succès',
          'Succès'
        );
        this.router.navigate(['/factures-clients']);
      },
      error: (err) => {
        this.toastr.error('Erreur lors de l\'enregistrement', 'Erreur');
        this.loading = false;
      }
    });
  }

  onCancel(): void {
    this.router.navigate(['/factures-clients']);
  }
}
