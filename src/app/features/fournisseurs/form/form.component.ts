import { Component, OnInit } from '@angular/core';
import { FormBuilder, FormGroup, Validators } from '@angular/forms';
import { ActivatedRoute, Router } from '@angular/router';
import { AchatsService } from '../../../core/services/achats.service';
import { ToastrService } from 'ngx-toastr';

@Component({
  selector: 'app-form',
  templateUrl: './form.component.html',
  styleUrl: './form.component.scss'
})
export class FormComponent implements OnInit {
  fournisseurForm!: FormGroup;
  isEditMode = false;
  fournisseurId?: number;
  loading = false;

  constructor(
    private fb: FormBuilder,
    private achatsService: AchatsService,
    private router: Router,
    private route: ActivatedRoute,
    private toastr: ToastrService
  ) {}

  ngOnInit(): void {
    this.initForm();
    this.checkEditMode();
  }

  initForm(): void {
    this.fournisseurForm = this.fb.group({
      nom: ['', [Validators.required, Validators.minLength(3)]],
      email: ['', [Validators.email]],
      telephone: ['', [Validators.required]],
      adresse: [''],
      ville: [''],
      pays: ['Sénégal']
    });
  }

  checkEditMode(): void {
    const id = this.route.snapshot.paramMap.get('id');
    if (id) {
      this.isEditMode = true;
      this.fournisseurId = +id;
      this.loadFournisseur();
    }
  }

  loadFournisseur(): void {
    if (!this.fournisseurId) return;

    this.loading = true;
    this.achatsService.getAllFournisseurs().subscribe({
      next: (response) => {
        const fournisseur = response.data.find((f: any) => f.id === this.fournisseurId);
        if (fournisseur) {
          this.fournisseurForm.patchValue(fournisseur);
        }
        this.loading = false;
      },
      error: (err) => {
        this.toastr.error('Erreur lors du chargement', 'Erreur');
        this.loading = false;
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

    const request = this.isEditMode
      ? this.achatsService.updateFournisseur(this.fournisseurId!, formData)
      : this.achatsService.createFournisseur(formData);

    request.subscribe({
      next: () => {
        this.toastr.success(
          this.isEditMode ? 'Fournisseur modifié avec succès' : 'Fournisseur créé avec succès',
          'Succès'
        );
        this.router.navigate(['/fournisseurs']);
      },
      error: (err) => {
        this.toastr.error(err.message || 'Erreur lors de l\'enregistrement', 'Erreur');
        this.loading = false;
      }
    });
  }

  onCancel(): void {
    this.router.navigate(['/fournisseurs']);
  }
}
