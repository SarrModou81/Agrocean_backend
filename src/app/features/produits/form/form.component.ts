import { Component, OnInit } from '@angular/core';
import { FormBuilder, FormGroup, Validators } from '@angular/forms';
import { ActivatedRoute, Router } from '@angular/router';
import { ToastrService } from 'ngx-toastr';
import { ProduitsService } from '../../../core/services/produits.service';
import { Categorie } from '../../../core/models';

@Component({
  selector: 'app-form',
  templateUrl: './form.component.html',
  styleUrl: './form.component.scss'
})
export class FormComponent implements OnInit {
  produitForm!: FormGroup;
  loading = false;
  isEditMode = false;
  produitId?: number;
  categories: Categorie[] = [];

  constructor(
    private fb: FormBuilder,
    private produitsService: ProduitsService,
    private route: ActivatedRoute,
    private router: Router,
    private toastr: ToastrService
  ) {}

  ngOnInit(): void {
    this.loadCategories();

    this.produitForm = this.fb.group({
      reference: ['', [Validators.required, Validators.minLength(3)]],
      nom: ['', [Validators.required, Validators.minLength(3)]],
      description: [''],
      categorie_id: ['', Validators.required],
      unite_mesure: ['', Validators.required],
      prix_achat: [0, [Validators.required, Validators.min(0)]],
      prix_vente: [0, [Validators.required, Validators.min(0)]],
      seuil_minimum: [10, [Validators.required, Validators.min(0)]],
      seuil_maximum: [1000, Validators.min(0)]
    });

    this.route.params.subscribe(params => {
      if (params['id']) {
        this.isEditMode = true;
        this.produitId = +params['id'];
        this.loadProduit();
      }
    });
  }

  loadCategories(): void {
    this.produitsService.getAllCategories().subscribe({
      next: (response) => {
        this.categories = response.data;
      },
      error: (err) => {
        this.toastr.error('Erreur lors du chargement des catégories', 'Erreur');
      }
    });
  }

  loadProduit(): void {
    if (!this.produitId) return;

    this.loading = true;
    this.produitsService.getById(this.produitId).subscribe({
      next: (response) => {
        this.produitForm.patchValue(response.data);
        this.loading = false;
      },
      error: (err) => {
        this.toastr.error('Erreur lors du chargement du produit', 'Erreur');
        this.loading = false;
        this.router.navigate(['/produits']);
      }
    });
  }

  onSubmit(): void {
    if (this.produitForm.invalid) {
      this.produitForm.markAllAsTouched();
      return;
    }

    this.loading = true;
    const formData = this.produitForm.value;

    const request = this.isEditMode && this.produitId
      ? this.produitsService.update(this.produitId, formData)
      : this.produitsService.create(formData);

    request.subscribe({
      next: (response) => {
        this.toastr.success(
          this.isEditMode ? 'Produit modifié avec succès' : 'Produit créé avec succès',
          'Succès'
        );
        this.router.navigate(['/produits']);
      },
      error: (err) => {
        this.toastr.error(err.message || 'Erreur lors de l\'enregistrement', 'Erreur');
        this.loading = false;
      }
    });
  }

  onCancel(): void {
    this.router.navigate(['/produits']);
  }
}
