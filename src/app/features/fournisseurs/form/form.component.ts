import { Component, OnInit } from '@angular/core';
import { FormBuilder, FormGroup, Validators } from '@angular/forms';
import { ActivatedRoute, Router } from '@angular/router';
import { ToastrService } from 'ngx-toastr';
import { FournisseursService } from '../../../core/services/fournisseurs.service';

@Component({
  selector: 'app-form',
  templateUrl: './form.component.html',
  styleUrl: './form.component.scss'
})
export class FormComponent implements OnInit {
  fournisseurForm!: FormGroup;
  loading = false;
  isEditMode = false;
  fournisseurId?: number;

  constructor(
    private fb: FormBuilder,
    private fournisseursService: FournisseursService,
    private route: ActivatedRoute,
    private router: Router,
    private toastr: ToastrService
  ) {}

  ngOnInit(): void {
    this.fournisseurForm = this.fb.group({
      nom: ['', [Validators.required, Validators.minLength(3)]],
      contact: [''],
      telephone: ['', [Validators.required]],
      adresse: [''],
      evaluation: [0],
      conditions: ['']
    });

    this.route.params.subscribe(params => {
      if (params['id']) {
        this.isEditMode = true;
        this.fournisseurId = +params['id'];
        this.loadFournisseur();
      }
    });
  }

  loadFournisseur(): void {
    if (!this.fournisseurId) return;

    this.loading = true;
    this.fournisseursService.getById(this.fournisseurId).subscribe({
      next: (response) => {
        this.fournisseurForm.patchValue(response.data);
        this.loading = false;
      },
      error: (err) => {
        this.toastr.error('Erreur lors du chargement', 'Erreur');
        this.loading = false;
        this.router.navigate(['/fournisseurs']);
      }
    });
  }

  onSubmit(): void {
    if (this.fournisseurForm.invalid) {
      this.fournisseurForm.markAllAsTouched();
      return;
    }

    this.loading = true;
    const formData = this.fournisseurForm.value;

    const request = this.isEditMode && this.fournisseurId
      ? this.fournisseursService.update(this.fournisseurId, formData)
      : this.fournisseursService.create(formData);

    request.subscribe({
      next: () => {
        this.toastr.success(
          this.isEditMode ? 'Fournisseur modifié avec succès' : 'Fournisseur créé avec succès',
          'Succès'
        );
        this.router.navigate(['/fournisseurs']);
      },
      error: (err) => {
        this.toastr.error('Erreur lors de l\'enregistrement', 'Erreur');
        this.loading = false;
      }
    });
  }

  onCancel(): void {
    this.router.navigate(['/fournisseurs']);
  }
}
