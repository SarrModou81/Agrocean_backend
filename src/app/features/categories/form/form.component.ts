import { Component, OnInit } from '@angular/core';
import { FormBuilder, FormGroup, Validators } from '@angular/forms';
import { ActivatedRoute, Router } from '@angular/router';
import { ToastrService } from 'ngx-toastr';
import { CategoriesService } from '../../../core/services/categories.service';

@Component({
  selector: 'app-form',
  templateUrl: './form.component.html',
  styleUrl: './form.component.scss'
})
export class FormComponent implements OnInit {
  categorieForm!: FormGroup;
  loading = false;
  isEditMode = false;
  categorieId?: number;

  constructor(
    private fb: FormBuilder,
    private categoriesService: CategoriesService,
    private route: ActivatedRoute,
    private router: Router,
    private toastr: ToastrService
  ) {}

  ngOnInit(): void {
    this.categorieForm = this.fb.group({
      nom: ['', [Validators.required, Validators.minLength(3)]],
      description: ['']
    });

    this.route.params.subscribe(params => {
      if (params['id']) {
        this.isEditMode = true;
        this.categorieId = +params['id'];
        this.loadCategorie();
      }
    });
  }

  loadCategorie(): void {
    if (!this.categorieId) return;

    this.loading = true;
    this.categoriesService.getById(this.categorieId).subscribe({
      next: (response) => {
        this.categorieForm.patchValue(response.data);
        this.loading = false;
      },
      error: (err) => {
        this.toastr.error('Erreur lors du chargement de la catégorie', 'Erreur');
        this.loading = false;
        this.router.navigate(['/categories']);
      }
    });
  }

  onSubmit(): void {
    if (this.categorieForm.invalid) {
      this.categorieForm.markAllAsTouched();
      return;
    }

    this.loading = true;
    const formData = this.categorieForm.value;

    const request = this.isEditMode && this.categorieId
      ? this.categoriesService.update(this.categorieId, formData)
      : this.categoriesService.create(formData);

    request.subscribe({
      next: (response) => {
        this.toastr.success(
          this.isEditMode ? 'Catégorie modifiée avec succès' : 'Catégorie créée avec succès',
          'Succès'
        );
        this.router.navigate(['/categories']);
      },
      error: (err) => {
        this.toastr.error(err.message || 'Erreur lors de l\'enregistrement', 'Erreur');
        this.loading = false;
      }
    });
  }

  onCancel(): void {
    this.router.navigate(['/categories']);
  }
}
