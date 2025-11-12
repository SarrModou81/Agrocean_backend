import { Component, OnInit } from '@angular/core';
import { FormBuilder, FormGroup, Validators } from '@angular/forms';
import { ActivatedRoute, Router } from '@angular/router';
import { ToastrService } from 'ngx-toastr';
import { ClientsService } from '../../../core/services/clients.service';

@Component({
  selector: 'app-form',
  templateUrl: './form.component.html',
  styleUrl: './form.component.scss'
})
export class FormComponent implements OnInit {
  clientForm!: FormGroup;
  loading = false;
  isEditMode = false;
  clientId?: number;

  typesClient = [
    { value: 'Particulier', label: 'Particulier' },
    { value: 'Entreprise', label: 'Entreprise' }
  ];

  constructor(
    private fb: FormBuilder,
    private clientsService: ClientsService,
    private route: ActivatedRoute,
    private router: Router,
    private toastr: ToastrService
  ) {}

  ngOnInit(): void {
    this.clientForm = this.fb.group({
      nom: ['', [Validators.required, Validators.minLength(3)]],
      email: ['', [Validators.email]],
      telephone: ['', [Validators.required]],
      adresse: [''],
      type: ['Particulier', Validators.required],
      credit_max: [0, [Validators.min(0)]],
      solde: [0]
    });

    this.route.params.subscribe(params => {
      if (params['id']) {
        this.isEditMode = true;
        this.clientId = +params['id'];
        this.loadClient();
      }
    });
  }

  loadClient(): void {
    if (!this.clientId) return;

    this.loading = true;
    this.clientsService.getById(this.clientId).subscribe({
      next: (response) => {
        this.clientForm.patchValue(response.data);
        this.loading = false;
      },
      error: (err) => {
        this.toastr.error('Erreur lors du chargement', 'Erreur');
        this.loading = false;
        this.router.navigate(['/clients']);
      }
    });
  }

  onSubmit(): void {
    if (this.clientForm.invalid) {
      this.clientForm.markAllAsTouched();
      return;
    }

    this.loading = true;
    const formData = this.clientForm.value;

    const request = this.isEditMode && this.clientId
      ? this.clientsService.update(this.clientId, formData)
      : this.clientsService.create(formData);

    request.subscribe({
      next: () => {
        this.toastr.success(
          this.isEditMode ? 'Client modifié avec succès' : 'Client créé avec succès',
          'Succès'
        );
        this.router.navigate(['/clients']);
      },
      error: (err) => {
        this.toastr.error('Erreur lors de l\'enregistrement', 'Erreur');
        this.loading = false;
      }
    });
  }

  onCancel(): void {
    this.router.navigate(['/clients']);
  }
}
