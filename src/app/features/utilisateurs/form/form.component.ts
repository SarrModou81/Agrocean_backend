import { Component, OnInit } from '@angular/core';
import { FormBuilder, FormGroup, Validators } from '@angular/forms';
import { ActivatedRoute, Router } from '@angular/router';
import { ToastrService } from 'ngx-toastr';
import { UtilisateursService } from '../../../core/services/utilisateurs.service';

@Component({
  selector: 'app-form',
  templateUrl: './form.component.html',
  styleUrl: './form.component.scss'
})
export class FormComponent implements OnInit {
  utilisateurForm!: FormGroup;
  loading = false;
  isEditMode = false;
  utilisateurId?: number;

  roles = [
    { value: 'admin', label: 'Administrateur' },
    { value: 'manager', label: 'Manager' },
    { value: 'employe', label: 'Employé' }
  ];

  constructor(
    private fb: FormBuilder,
    private utilisateursService: UtilisateursService,
    private route: ActivatedRoute,
    private router: Router,
    private toastr: ToastrService
  ) {}

  ngOnInit(): void {
    this.utilisateurForm = this.fb.group({
      nom: ['', [Validators.required, Validators.minLength(2)]],
      prenom: ['', [Validators.required, Validators.minLength(2)]],
      email: ['', [Validators.required, Validators.email]],
      telephone: [''],
      role: ['employe', Validators.required],
      actif: [true],
      password: [''],
      password_confirmation: ['']
    });

    this.route.params.subscribe(params => {
      if (params['id']) {
        this.isEditMode = true;
        this.utilisateurId = +params['id'];
        this.loadUtilisateur();
        // Password not required for edit
        this.utilisateurForm.get('password')?.clearValidators();
        this.utilisateurForm.get('password_confirmation')?.clearValidators();
      } else {
        // Password required for create
        this.utilisateurForm.get('password')?.setValidators([Validators.required, Validators.minLength(6)]);
        this.utilisateurForm.get('password_confirmation')?.setValidators([Validators.required]);
      }
      this.utilisateurForm.get('password')?.updateValueAndValidity();
      this.utilisateurForm.get('password_confirmation')?.updateValueAndValidity();
    });
  }

  loadUtilisateur(): void {
    if (!this.utilisateurId) return;

    this.loading = true;
    this.utilisateursService.getById(this.utilisateurId).subscribe({
      next: (response) => {
        this.utilisateurForm.patchValue(response.data);
        this.loading = false;
      },
      error: (err) => {
        this.toastr.error('Erreur lors du chargement de l\'utilisateur', 'Erreur');
        this.loading = false;
        this.router.navigate(['/utilisateurs']);
      }
    });
  }

  onSubmit(): void {
    if (this.utilisateurForm.invalid) {
      this.utilisateurForm.markAllAsTouched();
      return;
    }

    // Check password confirmation
    const password = this.utilisateurForm.get('password')?.value;
    const confirmation = this.utilisateurForm.get('password_confirmation')?.value;

    if (password && password !== confirmation) {
      this.toastr.error('Les mots de passe ne correspondent pas', 'Erreur');
      return;
    }

    this.loading = true;
    const formData = { ...this.utilisateurForm.value };

    // Remove password fields if empty in edit mode
    if (this.isEditMode && !formData.password) {
      delete formData.password;
      delete formData.password_confirmation;
    }

    const request = this.isEditMode && this.utilisateurId
      ? this.utilisateursService.update(this.utilisateurId, formData)
      : this.utilisateursService.create(formData);

    request.subscribe({
      next: (response) => {
        this.toastr.success(
          this.isEditMode ? 'Utilisateur modifié avec succès' : 'Utilisateur créé avec succès',
          'Succès'
        );
        this.router.navigate(['/utilisateurs']);
      },
      error: (err) => {
        this.toastr.error(err.message || 'Erreur lors de l\'enregistrement', 'Erreur');
        this.loading = false;
      }
    });
  }

  onCancel(): void {
    this.router.navigate(['/utilisateurs']);
  }
}
