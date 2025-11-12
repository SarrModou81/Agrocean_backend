import { Component, OnInit } from '@angular/core';
import { FormBuilder, FormGroup, Validators, FormArray } from '@angular/forms';
import { ActivatedRoute, Router } from '@angular/router';
import { ToastrService } from 'ngx-toastr';
import { Observable } from 'rxjs';
import { map, startWith } from 'rxjs/operators';
import { CommandesAchatService } from '../../../core/services/commandes-achat.service';
import { FournisseursService } from '../../../core/services/fournisseurs.service';
import { ProduitsService } from '../../../core/services/produits.service';
import { Fournisseur, Produit } from '../../../core/models';

@Component({
  selector: 'app-form',
  templateUrl: './form.component.html',
  styleUrl: './form.component.scss'
})
export class FormComponent implements OnInit {
  commandeForm!: FormGroup;
  loading = false;
  isEditMode = false;
  commandeId?: number;

  fournisseurs: Fournisseur[] = [];
  produits: Produit[] = [];
  filteredProduits: Observable<Produit[]>[] = [];

  statuts = [
    { value: 'Brouillon', label: 'Brouillon' },
    { value: 'Validée', label: 'Validée' },
    { value: 'Reçue', label: 'Reçue' },
    { value: 'Annulée', label: 'Annulée' }
  ];

  constructor(
    private fb: FormBuilder,
    private commandesAchatService: CommandesAchatService,
    private fournisseursService: FournisseursService,
    private produitsService: ProduitsService,
    private route: ActivatedRoute,
    private router: Router,
    private toastr: ToastrService
  ) {}

  ngOnInit(): void {
    this.commandeForm = this.fb.group({
      fournisseur_id: ['', Validators.required],
      date_commande: [new Date(), Validators.required],
      date_livraison_prevue: ['', Validators.required],
      statut: ['Brouillon', Validators.required],
      details: this.fb.array([])
    });

    this.loadFournisseurs();
    this.loadProduits();
    this.addDetail();

    this.route.params.subscribe(params => {
      if (params['id']) {
        this.isEditMode = true;
        this.commandeId = +params['id'];
        this.loadCommande();
      }
    });
  }

  get details(): FormArray {
    return this.commandeForm.get('details') as FormArray;
  }

  createDetail(): FormGroup {
    return this.fb.group({
      produit_id: ['', Validators.required],
      quantite: [1, [Validators.required, Validators.min(1)]],
      prix_unitaire: [0, [Validators.required, Validators.min(0)]],
      produit_search: ['']
    });
  }

  addDetail(): void {
    const detail = this.createDetail();
    this.details.push(detail);

    const index = this.details.length - 1;
    this.filteredProduits[index] = detail.get('produit_search')!.valueChanges.pipe(
      startWith(''),
      map(value => this._filterProduits(value || ''))
    );
  }

  removeDetail(index: number): void {
    this.details.removeAt(index);
    this.filteredProduits.splice(index, 1);
  }

  onProduitSelected(index: number, produit: Produit): void {
    const detail = this.details.at(index);
    detail.patchValue({
      produit_id: produit.id,
      prix_unitaire: produit.prix_vente || 0
    });
  }

  displayProduitFn(produitId: number): string {
    const produit = this.produits.find(p => p.id === produitId);
    return produit ? `${produit.nom} - ${produit.reference}` : '';
  }

  private _filterProduits(value: string): Produit[] {
    const filterValue = value.toLowerCase();
    return this.produits.filter(p =>
      p.nom.toLowerCase().includes(filterValue) ||
      p.reference.toLowerCase().includes(filterValue)
    );
  }

  getMontantTotal(): number {
    return this.details.controls.reduce((total, detail) => {
      const quantite = detail.get('quantite')?.value || 0;
      const prix = detail.get('prix_unitaire')?.value || 0;
      return total + (quantite * prix);
    }, 0);
  }

  loadFournisseurs(): void {
    this.fournisseursService.getAll({ per_page: 1000 }).subscribe({
      next: (response) => {
        this.fournisseurs = response.data;
      },
      error: (err) => {
        this.toastr.error('Erreur lors du chargement des fournisseurs', 'Erreur');
      }
    });
  }

  loadProduits(): void {
    this.produitsService.getAll({ per_page: 1000 }).subscribe({
      next: (response) => {
        this.produits = response.data;
      },
      error: (err) => {
        this.toastr.error('Erreur lors du chargement des produits', 'Erreur');
      }
    });
  }

  loadCommande(): void {
    if (!this.commandeId) return;

    this.loading = true;
    this.commandesAchatService.getById(this.commandeId).subscribe({
      next: (response) => {
        const commande = response.data;
        this.commandeForm.patchValue({
          fournisseur_id: commande.fournisseur_id,
          date_commande: new Date(commande.date_commande),
          date_livraison_prevue: new Date(commande.date_livraison_prevue),
          statut: commande.statut
        });

        // Clear existing details and add from loaded data
        this.details.clear();
        if (commande.details && commande.details.length > 0) {
          commande.details.forEach(detail => {
            const detailGroup = this.createDetail();
            detailGroup.patchValue({
              produit_id: detail.produit_id,
              quantite: detail.quantite,
              prix_unitaire: detail.prix_unitaire
            });
            this.details.push(detailGroup);

            const index = this.details.length - 1;
            this.filteredProduits[index] = detailGroup.get('produit_search')!.valueChanges.pipe(
              startWith(''),
              map(value => this._filterProduits(value || ''))
            );
          });
        }

        this.loading = false;
      },
      error: (err) => {
        this.toastr.error('Erreur lors du chargement', 'Erreur');
        this.loading = false;
        this.router.navigate(['/commandes-achat']);
      }
    });
  }

  onSubmit(): void {
    if (this.commandeForm.invalid) {
      this.commandeForm.markAllAsTouched();
      return;
    }

    if (this.details.length === 0) {
      this.toastr.error('Veuillez ajouter au moins un produit', 'Erreur');
      return;
    }

    this.loading = true;
    const formData = {
      ...this.commandeForm.value,
      montant_total: this.getMontantTotal()
    };

    // Remove produit_search from details
    formData.details = formData.details.map((d: any) => ({
      produit_id: d.produit_id,
      quantite: d.quantite,
      prix_unitaire: d.prix_unitaire
    }));

    const request = this.isEditMode && this.commandeId
      ? this.commandesAchatService.update(this.commandeId, formData)
      : this.commandesAchatService.create(formData);

    request.subscribe({
      next: () => {
        this.toastr.success(
          this.isEditMode ? 'Commande modifiée avec succès' : 'Commande créée avec succès',
          'Succès'
        );
        this.router.navigate(['/commandes-achat']);
      },
      error: (err) => {
        this.toastr.error('Erreur lors de l\'enregistrement', 'Erreur');
        this.loading = false;
      }
    });
  }

  onCancel(): void {
    this.router.navigate(['/commandes-achat']);
  }
}
