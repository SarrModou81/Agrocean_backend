import { Component, OnInit } from '@angular/core';
import { FormBuilder, FormGroup, FormArray, Validators } from '@angular/forms';
import { Router, ActivatedRoute } from '@angular/router';
import { AchatsService } from '../../../core/services/achats.service';
import { ProduitsService } from '../../../core/services/produits.service';
import { ToastrService } from 'ngx-toastr';
import { Fournisseur, Produit } from '../../../core/models';

@Component({
  selector: 'app-form',
  templateUrl: './form.component.html',
  styleUrl: './form.component.scss'
})
export class FormComponent implements OnInit {
  achatForm!: FormGroup;
  fournisseurs: Fournisseur[] = [];
  produits: Produit[] = [];
  loading = false;
  isEditMode = false;
  achatId?: number;

  constructor(
    private fb: FormBuilder,
    private router: Router,
    private route: ActivatedRoute,
    private achatsService: AchatsService,
    private produitsService: ProduitsService,
    private toastr: ToastrService
  ) {}

  ngOnInit(): void {
    this.initForm();
    this.loadFournisseurs();
    this.loadProduits();

    // Check if editing existing achat
    this.route.params.subscribe(params => {
      if (params['id']) {
        this.isEditMode = true;
        this.achatId = +params['id'];
        this.loadAchat(this.achatId);
      }
    });

    // Add one initial line
    if (!this.isEditMode) {
      this.addLigne();
    }
  }

  initForm(): void {
    this.achatForm = this.fb.group({
      fournisseur_id: ['', [Validators.required]],
      date_commande: [new Date().toISOString().split('T')[0], [Validators.required]],
      date_livraison_prevue: [''],
      lignes: this.fb.array([])
    });

    // Recalculate totals when form changes
    this.achatForm.valueChanges.subscribe(() => {
      this.calculateTotals();
    });
  }

  get lignes(): FormArray {
    return this.achatForm.get('lignes') as FormArray;
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
          prix_unitaire: produit.prix_achat || produit.prix_vente || 0
        });
      }
    }
  }

  calculateTotals(): any {
    let montantTotal = 0;

    this.lignes.controls.forEach((ligne: any) => {
      const quantite = ligne.get('quantite')?.value || 0;
      const prixUnitaire = ligne.get('prix_unitaire')?.value || 0;
      montantTotal += quantite * prixUnitaire;
    });

    return {
      montantTotal: montantTotal.toFixed(2)
    };
  }

  loadFournisseurs(): void {
    this.achatsService.getAllFournisseurs().subscribe({
      next: (response) => {
        this.fournisseurs = response.data;
      },
      error: (err) => {
        this.toastr.error('Erreur lors du chargement des fournisseurs', 'Erreur');
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

  loadAchat(id: number): void {
    this.loading = true;
    this.achatsService.getById(id).subscribe({
      next: (response) => {
        const achat = response.data;
        this.achatForm.patchValue({
          fournisseur_id: achat.fournisseur_id,
          date_commande: achat.date_commande,
          date_livraison_prevue: achat.date_livraison_prevue || ''
        });

        // Clear existing lines and add achat lines
        this.lignes.clear();
        if (achat.detail_commande_achats) {
          achat.detail_commande_achats.forEach(detail => {
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
        this.toastr.error('Erreur lors du chargement de la commande', 'Erreur');
        this.loading = false;
      }
    });
  }

  onSubmit(): void {
    if (this.achatForm.invalid) {
      this.achatForm.markAllAsTouched();
      this.toastr.warning('Veuillez remplir tous les champs requis', 'Attention');
      return;
    }

    if (this.lignes.length === 0) {
      this.toastr.warning('Veuillez ajouter au moins une ligne de produit', 'Attention');
      return;
    }

    this.loading = true;
    const formData = {
      ...this.achatForm.value,
      detail_commande_achats: this.lignes.value.map((ligne: any) => ({
        produit_id: ligne.produit_id,
        quantite: ligne.quantite,
        prix_unitaire: ligne.prix_unitaire
      }))
    };

    const request = this.isEditMode && this.achatId
      ? this.achatsService.update(this.achatId, formData)
      : this.achatsService.create(formData);

    request.subscribe({
      next: (response) => {
        this.toastr.success(
          this.isEditMode ? 'Commande modifiée avec succès' : 'Commande créée avec succès',
          'Succès'
        );
        this.router.navigate(['/achats']);
      },
      error: (err) => {
        this.toastr.error(
          err.message || 'Erreur lors de l\'enregistrement de la commande',
          'Erreur'
        );
        this.loading = false;
      }
    });
  }

  onCancel(): void {
    this.router.navigate(['/achats']);
  }
}
