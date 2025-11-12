import { Component, OnInit } from '@angular/core';
import { FormBuilder, FormGroup, Validators } from '@angular/forms';
import { ToastrService } from 'ngx-toastr';

@Component({
  selector: 'app-settings',
  templateUrl: './settings.component.html',
  styleUrl: './settings.component.scss'
})
export class SettingsComponent implements OnInit {
  generalForm!: FormGroup;
  notificationForm!: FormGroup;
  loading = false;

  constructor(
    private fb: FormBuilder,
    private toastr: ToastrService
  ) {}

  ngOnInit(): void {
    this.generalForm = this.fb.group({
      nom_entreprise: ['AGROCEAN', Validators.required],
      email: ['contact@agrocean.com', [Validators.required, Validators.email]],
      telephone: ['+221 77 123 45 67'],
      adresse: ['Dakar, Sénégal'],
      devise: ['FCFA', Validators.required],
      langue: ['fr', Validators.required],
      fuseau_horaire: ['Africa/Dakar', Validators.required]
    });

    this.notificationForm = this.fb.group({
      email_notifications: [true],
      stock_alerts: [true],
      low_stock_threshold: [10, [Validators.required, Validators.min(0)]],
      ventes_notifications: [true],
      achats_notifications: [false]
    });
  }

  onSaveGeneral(): void {
    if (this.generalForm.invalid) {
      this.generalForm.markAllAsTouched();
      return;
    }

    this.loading = true;
    // Simulate API call
    setTimeout(() => {
      this.toastr.success('Paramètres généraux enregistrés avec succès', 'Succès');
      this.loading = false;
    }, 1000);
  }

  onSaveNotifications(): void {
    if (this.notificationForm.invalid) {
      this.notificationForm.markAllAsTouched();
      return;
    }

    this.loading = true;
    // Simulate API call
    setTimeout(() => {
      this.toastr.success('Préférences de notification enregistrées avec succès', 'Succès');
      this.loading = false;
    }, 1000);
  }
}
