import { Component, OnInit } from '@angular/core';
import { FormBuilder, FormGroup, FormArray, Validators } from '@angular/forms';
import { Router, ActivatedRoute } from '@angular/router';
import { VentesService } from '../../../core/services/ventes.service';
import { ProduitsService } from '../../../core/services/produits.service';
import { ToastrService } from 'ngx-toastr';
import { Client, Produit } from '../../../core/models';

@Component({
  selector: 'app-form',
  templateUrl: './form.component.html',
  styleUrl: './form.component.scss'
})
export class FormComponent implements OnInit {
  venteForm!: FormGroup;
  clients: Client[] = [];
  produits: Produit[] = [];
  loading = false;
  isEditMode = false;
  venteId?: number;

  constructor(
    private fb: FormBuilder,
    private router: Router,
    private route: ActivatedRoute,
    private ventesService: VentesService,
    private produitsService: ProduitsService,
    private toastr: ToastrService
  ) {}

  ngOnInit(): void {
    this.initForm();
    this.loadClients();
    this.loadProduits();

    // Check if editing existing vente
    this.route.params.subscribe(params => {
      if (params['id']) {
        this.isEditMode = true;
        this.venteId = +params['id'];
        this.loadVente(this.venteId);
      }
    });

    // Add one initial line
    if (!this.isEditMode) {
      this.addLigne();
    }
  }

  initForm(): void {
    this.venteForm = this.fb.group({
      client_id: ['', [Validators.required]],
      date_vente: [new Date().toISOString().split('T')[0], [Validators.required]],
      remise: [0, [Validators.min(0), Validators.max(100)]],
      lignes: this.fb.array([])
    });

    // Recalculate totals when form changes
    this.venteForm.valueChanges.subscribe(() => {
      this.calculateTotals();
    });
  }

  get lignes(): FormArray {
    return this.venteForm.get('lignes') as FormArray;
  }

  addLigne(): void {
    const ligneGroup = this.fb.group({
      produit_id: ['', [Validators.required]],
      quantite: [1, [Validators.required, Validators.min(1)]],
      prix_unitaire: [0, [Validators.required, Validators.min(0)]],
      sous_total: [{ value: 0, disabled: true }]
    });

    ligneGroup.valueChanges.subscribe(() => {
      this.updateLigneTotal(ligneGroup);
    });

    this.lignes.push(ligneGroup);
  }

  removeLigne(index: number): void {
    if (this.lignes.length > 1) {
      this.lignes.removeAt(index);
    } else {
      this.toastr.warning('Au moins une ligne est requise', 'Attention');
    }
  }

  updateLigneTotal(ligneGroup: FormGroup): void {
    const quantite = ligneGroup.get('quantite')?.value || 0;
    const prixUnitaire = ligneGroup.get('prix_unitaire')?.value || 0;
    const sousTotal = quantite * prixUnitaire;
    ligneGroup.get('sous_total')?.setValue(sousTotal, { emitEvent: false });
  }

  onProduitSelect(index: number): void {
    const ligne = this.lignes.at(index) as FormGroup;
    const produitId = ligne.get('produit_id')?.value;

    if (produitId) {
      const produit = this.produits.find(p => p.id === +produitId);
      if (produit) {
        ligne.patchValue({
          prix_unitaire: produit.prix_vente || 0
        });
      }
    }
  }

  calculateTotals(): any {
    let montantHT = 0;

    this.lignes.controls.forEach((ligne: any) => {
      const quantite = ligne.get('quantite')?.value || 0;
      const prixUnitaire = ligne.get('prix_unitaire')?.value || 0;
      montantHT += quantite * prixUnitaire;
    });

    const remise = this.venteForm.get('remise')?.value || 0;
    const montantRemise = montantHT * (remise / 100);
    const montantApresRemise = montantHT - montantRemise;
    const montantTVA = montantApresRemise * 0.18; // TVA 18%
    const montantTTC = montantApresRemise + montantTVA;

    return {
      montantHT: montantHT.toFixed(2),
      montantRemise: montantRemise.toFixed(2),
      montantApresRemise: montantApresRemise.toFixed(2),
      montantTVA: montantTVA.toFixed(2),
      montantTTC: montantTTC.toFixed(2)
    };
  }

  loadClients(): void {
    this.ventesService.getAllClients().subscribe({
      next: (response) => {
        this.clients = response.data;
      },
      error: (err) => {
        this.toastr.error('Erreur lors du chargement des clients', 'Erreur');
      }
    });
  }

  loadProduits(): void {
    this.produitsService.getAll().subscribe({
      next: (response) => {
        this.produits = response.data;
      },
      error: (err) => {
        this.toastr.error('Erreur lors du chargement des produits', 'Erreur');
      }
    });
  }

  loadVente(id: number): void {
    this.loading = true;
    this.ventesService.getById(id).subscribe({
      next: (response) => {
        const vente = response.data;
        this.venteForm.patchValue({
          client_id: vente.client_id,
          date_vente: vente.date_vente,
          remise: vente.remise || 0
        });

        // Clear existing lines and add vente lines
        this.lignes.clear();
        if (vente.detail_ventes) {
          vente.detail_ventes.forEach(detail => {
            const ligneGroup = this.fb.group({
              produit_id: [detail.produit_id, [Validators.required]],
              quantite: [detail.quantite, [Validators.required, Validators.min(1)]],
              prix_unitaire: [detail.prix_unitaire, [Validators.required, Validators.min(0)]],
              sous_total: [{ value: detail.sous_total, disabled: true }]
            });
            this.lignes.push(ligneGroup);
          });
        }

        this.loading = false;
      },
      error: (err) => {
        this.toastr.error('Erreur lors du chargement de la vente', 'Erreur');
        this.loading = false;
      }
    });
  }

  onSubmit(): void {
    if (this.venteForm.invalid) {
      this.venteForm.markAllAsTouched();
      this.toastr.warning('Veuillez remplir tous les champs requis', 'Attention');
      return;
    }

    if (this.lignes.length === 0) {
      this.toastr.warning('Veuillez ajouter au moins une ligne de produit', 'Attention');
      return;
    }

    this.loading = true;
    const formData = {
      ...this.venteForm.value,
      detail_ventes: this.lignes.value.map((ligne: any) => ({
        produit_id: ligne.produit_id,
        quantite: ligne.quantite,
        prix_unitaire: ligne.prix_unitaire
      }))
    };

    const request = this.isEditMode && this.venteId
      ? this.ventesService.update(this.venteId, formData)
      : this.ventesService.create(formData);

    request.subscribe({
      next: (response) => {
        this.toastr.success(
          this.isEditMode ? 'Vente modifiée avec succès' : 'Vente créée avec succès',
          'Succès'
        );
        this.router.navigate(['/ventes']);
      },
      error: (err) => {
        this.toastr.error(
          err.message || 'Erreur lors de l\'enregistrement de la vente',
          'Erreur'
        );
        this.loading = false;
      }
    });
  }

  onCancel(): void {
    this.router.navigate(['/ventes']);
  }
}
