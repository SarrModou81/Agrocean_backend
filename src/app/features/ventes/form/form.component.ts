import { Component, OnInit } from '@angular/core';
import { Router } from '@angular/router';

@Component({
  selector: 'app-form',
  templateUrl: './form.component.html',
  styleUrl: './form.component.scss'
})
export class FormComponent implements OnInit {
  constructor(private router: Router) {}

  ngOnInit(): void {
    // Formulaire en cours de développement
  }

  onCancel(): void {
    this.router.navigate(['/ventes']);
  }
}
