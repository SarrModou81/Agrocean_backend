import { Component, OnInit } from '@angular/core';
import { FormBuilder, FormGroup, Validators } from '@angular/forms';
import { ActivatedRoute, Router } from '@angular/router';
import { ToastrService } from 'ngx-toastr';
import { PaiementsService } from '../../../core/services/paiements.service';
import { FacturesService } from '../../../core/services/factures.service';
import { Facture, FactureFournisseur } from '../../../core/models';

@Component({
  selector: 'app-form',
  templateUrl: './form.component.html',
  styleUrl: './form.component.scss'
})
export class FormComponent implements OnInit {
  paiementForm!: FormGroup;
  loading = false;
  isEditMode = false;
  paiementId?: number;

  facturesClients: Facture[] = [];
  facturesFournisseurs: FactureFournisseur[] = [];

  types = [
    { value: 'Client', label: 'Client' },
    { value: 'Fournisseur', label: 'Fournisseur' }
  ];

  modesPaiement = [
    { value: 'Espèces', label: 'Espèces' },
    { value: 'Chèque', label: 'Chèque' },
    { value: 'Virement', label: 'Virement' },
    { value: 'Mobile Money', label: 'Mobile Money' },
    { value: 'Carte bancaire', label: 'Carte bancaire' }
  ];

  constructor(
    private fb: FormBuilder,
    private paiementsService: PaiementsService,
    private facturesService: FacturesService,
    private route: ActivatedRoute,
    private router: Router,
    private toastr: ToastrService
  ) {}

  ngOnInit(): void {
    this.paiementForm = this.fb.group({
      type: ['Client', Validators.required],
      facture_id: [''],
      facture_fournisseur_id: [''],
      montant: ['', [Validators.required, Validators.min(0)]],
      mode_paiement: ['', Validators.required],
      date_paiement: [new Date(), Validators.required],
      reference: [''],
      notes: ['']
    });

    this.loadFactures();

    // Update validation based on type
    this.paiementForm.get('type')?.valueChanges.subscribe(type => {
      this.updateFactureValidation(type);
    });

    this.route.params.subscribe(params => {
      if (params['id']) {
        this.isEditMode = true;
        this.paiementId = +params['id'];
        this.loadPaiement();
      }
    });

    this.updateFactureValidation('Client');
  }

  updateFactureValidation(type: string): void {
    const factureControl = this.paiementForm.get('facture_id');
    const factureFournisseurControl = this.paiementForm.get('facture_fournisseur_id');

    if (type === 'Client') {
      factureControl?.setValidators([Validators.required]);
      factureFournisseurControl?.clearValidators();
      factureFournisseurControl?.setValue('');
    } else {
      factureFournisseurControl?.setValidators([Validators.required]);
      factureControl?.clearValidators();
      factureControl?.setValue('');
    }

    factureControl?.updateValueAndValidity();
    factureFournisseurControl?.updateValueAndValidity();
  }

  loadFactures(): void {
    // Load client invoices
    this.facturesService.getAll({ per_page: 1000 }).subscribe({
      next: (response) => {
        this.facturesClients = response.data;
      },
      error: (err) => {
        this.toastr.error('Erreur lors du chargement des factures clients', 'Erreur');
      }
    });

    // Load supplier invoices
    this.facturesService.getFournisseursAll({ per_page: 1000 }).subscribe({
      next: (response) => {
        this.facturesFournisseurs = response.data;
      },
      error: (err) => {
        this.toastr.error('Erreur lors du chargement des factures fournisseurs', 'Erreur');
      }
    });
  }

  loadPaiement(): void {
    if (!this.paiementId) return;

    this.loading = true;
    this.paiementsService.getById(this.paiementId).subscribe({
      next: (response) => {
        const paiement = response.data;
        this.paiementForm.patchValue({
          type: paiement.type,
          facture_id: paiement.facture_id,
          facture_fournisseur_id: paiement.facture_fournisseur_id,
          montant: paiement.montant,
          mode_paiement: paiement.mode_paiement,
          date_paiement: new Date(paiement.date_paiement),
          reference: paiement.reference,
          notes: paiement.notes
        });
        this.loading = false;
      },
      error: (err) => {
        this.toastr.error('Erreur lors du chargement', 'Erreur');
        this.loading = false;
        this.router.navigate(['/paiements']);
      }
    });
  }

  onSubmit(): void {
    if (this.paiementForm.invalid) {
      this.paiementForm.markAllAsTouched();
      return;
    }

    this.loading = true;
    const formData = this.paiementForm.value;

    const request = this.isEditMode && this.paiementId
      ? this.paiementsService.update(this.paiementId, formData)
      : this.paiementsService.create(formData);

    request.subscribe({
      next: () => {
        this.toastr.success(
          this.isEditMode ? 'Paiement modifié avec succès' : 'Paiement créé avec succès',
          'Succès'
        );
        this.router.navigate(['/paiements']);
      },
      error: (err) => {
        this.toastr.error('Erreur lors de l\'enregistrement', 'Erreur');
        this.loading = false;
      }
    });
  }

  onCancel(): void {
    this.router.navigate(['/paiements']);
  }
}
