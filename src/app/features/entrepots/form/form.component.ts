import { Component, OnInit } from '@angular/core';
import { FormBuilder, FormGroup, Validators } from '@angular/forms';
import { ActivatedRoute, Router } from '@angular/router';
import { ToastrService } from 'ngx-toastr';
import { EntrepotsService } from '../../../core/services/entrepots.service';

@Component({
  selector: 'app-form',
  templateUrl: './form.component.html',
  styleUrl: './form.component.scss'
})
export class FormComponent implements OnInit {
  entrepotForm!: FormGroup;
  loading = false;
  isEditMode = false;
  entrepotId?: number;

  constructor(
    private fb: FormBuilder,
    private entrepotsService: EntrepotsService,
    private route: ActivatedRoute,
    private router: Router,
    private toastr: ToastrService
  ) {}

  ngOnInit(): void {
    this.entrepotForm = this.fb.group({
      nom: ['', [Validators.required, Validators.minLength(3)]],
      adresse: ['', Validators.required],
      ville: [''],
      telephone: [''],
      capacite: [''],
      responsable: ['']
    });

    this.route.params.subscribe(params => {
      if (params['id']) {
        this.isEditMode = true;
        this.entrepotId = +params['id'];
        this.loadEntrepot();
      }
    });
  }

  loadEntrepot(): void {
    if (!this.entrepotId) return;

    this.loading = true;
    this.entrepotsService.getById(this.entrepotId).subscribe({
      next: (response) => {
        this.entrepotForm.patchValue(response.data);
        this.loading = false;
      },
      error: (err) => {
        this.toastr.error('Erreur lors du chargement de l\'entrepôt', 'Erreur');
        this.loading = false;
        this.router.navigate(['/entrepots']);
      }
    });
  }

  onSubmit(): void {
    if (this.entrepotForm.invalid) {
      this.entrepotForm.markAllAsTouched();
      return;
    }

    this.loading = true;
    const formData = this.entrepotForm.value;

    const request = this.isEditMode && this.entrepotId
      ? this.entrepotsService.update(this.entrepotId, formData)
      : this.entrepotsService.create(formData);

    request.subscribe({
      next: (response) => {
        this.toastr.success(
          this.isEditMode ? 'Entrepôt modifié avec succès' : 'Entrepôt créé avec succès',
          'Succès'
        );
        this.router.navigate(['/entrepots']);
      },
      error: (err) => {
        this.toastr.error(err.message || 'Erreur lors de l\'enregistrement', 'Erreur');
        this.loading = false;
      }
    });
  }

  onCancel(): void {
    this.router.navigate(['/entrepots']);
  }
}
