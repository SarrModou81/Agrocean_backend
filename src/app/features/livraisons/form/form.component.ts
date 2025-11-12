import { Component, OnInit } from '@angular/core';
import { FormBuilder, FormGroup, Validators } from '@angular/forms';
import { ActivatedRoute, Router } from '@angular/router';
import { ToastrService } from 'ngx-toastr';
import { LivraisonsService } from '../../../core/services/livraisons.service';
import { VentesService } from '../../../core/services/ventes.service';
import { Vente } from '../../../core/models';

@Component({
  selector: 'app-form',
  templateUrl: './form.component.html',
  styleUrl: './form.component.scss'
})
export class FormComponent implements OnInit {
  livraisonForm!: FormGroup;
  loading = false;
  isEditMode = false;
  livraisonId?: number;

  ventes: Vente[] = [];

  statuts = [
    { value: 'En préparation', label: 'En préparation' },
    { value: 'En cours', label: 'En cours' },
    { value: 'Livrée', label: 'Livrée' },
    { value: 'Annulée', label: 'Annulée' }
  ];

  constructor(
    private fb: FormBuilder,
    private livraisonsService: LivraisonsService,
    private ventesService: VentesService,
    private route: ActivatedRoute,
    private router: Router,
    private toastr: ToastrService
  ) {}

  ngOnInit(): void {
    this.livraisonForm = this.fb.group({
      vente_id: ['', Validators.required],
      adresse_livraison: ['', Validators.required],
      date_livraison_prevue: ['', Validators.required],
      statut: ['En préparation', Validators.required],
      notes: ['']
    });

    this.loadVentes();

    this.route.params.subscribe(params => {
      if (params['id']) {
        this.isEditMode = true;
        this.livraisonId = +params['id'];
        this.loadLivraison();
      }
    });
  }

  loadVentes(): void {
    this.ventesService.getAll({ per_page: 1000 }).subscribe({
      next: (response) => {
        this.ventes = response.data;
      },
      error: (err) => {
        this.toastr.error('Erreur lors du chargement des ventes', 'Erreur');
      }
    });
  }

  loadLivraison(): void {
    if (!this.livraisonId) return;

    this.loading = true;
    this.livraisonsService.getById(this.livraisonId).subscribe({
      next: (response) => {
        const livraison = response.data;
        this.livraisonForm.patchValue({
          vente_id: livraison.vente_id,
          adresse_livraison: livraison.adresse_livraison,
          date_livraison_prevue: livraison.date_livraison_prevue ? new Date(livraison.date_livraison_prevue) : undefined,
          statut: livraison.statut,
          notes: livraison.notes
        });
        this.loading = false;
      },
      error: (err) => {
        this.toastr.error('Erreur lors du chargement', 'Erreur');
        this.loading = false;
        this.router.navigate(['/livraisons']);
      }
    });
  }

  onSubmit(): void {
    if (this.livraisonForm.invalid) {
      this.livraisonForm.markAllAsTouched();
      return;
    }

    this.loading = true;
    const formData = this.livraisonForm.value;

    const request = this.isEditMode && this.livraisonId
      ? this.livraisonsService.update(this.livraisonId, formData)
      : this.livraisonsService.create(formData);

    request.subscribe({
      next: () => {
        this.toastr.success(
          this.isEditMode ? 'Livraison modifiée avec succès' : 'Livraison créée avec succès',
          'Succès'
        );
        this.router.navigate(['/livraisons']);
      },
      error: (err) => {
        this.toastr.error('Erreur lors de l\'enregistrement', 'Erreur');
        this.loading = false;
      }
    });
  }

  onCancel(): void {
    this.router.navigate(['/livraisons']);
  }
}
