import { Component, OnInit } from '@angular/core';
import { AchatsService } from '../../../core/services/achats.service';
import { Fournisseur } from '../../../core/models';
import { ToastrService } from 'ngx-toastr';

@Component({
  selector: 'app-liste',
  templateUrl: './liste.component.html',
  styleUrl: './liste.component.scss'
})
export class ListeComponent implements OnInit {
  displayedColumns: string[] = ['nom', 'email', 'telephone', 'adresse'];
  fournisseurs: Fournisseur[] = [];
  loading = false;

  constructor(
    private achatsService: AchatsService,
    private toastr: ToastrService
  ) {}

  ngOnInit(): void {
    this.loadFournisseurs();
  }

  loadFournisseurs(): void {
    this.loading = true;
    this.achatsService.getAllFournisseurs().subscribe({
      next: (response) => {
        this.fournisseurs = response.data;
        this.loading = false;
      },
      error: (err) => {
        this.toastr.error('Erreur lors du chargement', 'Erreur');
        this.loading = false;
      }
    });
  }
}
