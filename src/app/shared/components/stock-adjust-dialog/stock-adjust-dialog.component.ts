import { Component, Inject, OnInit } from '@angular/core';
import { FormBuilder, FormGroup, Validators } from '@angular/forms';
import { MatDialogRef, MAT_DIALOG_DATA } from '@angular/material/dialog';

export interface StockAdjustDialogData {
  stockId: number;
  produitNom: string;
  quantiteActuelle: number;
}

@Component({
  selector: 'app-stock-adjust-dialog',
  templateUrl: './stock-adjust-dialog.component.html',
  styleUrl: './stock-adjust-dialog.component.scss'
})
export class StockAdjustDialogComponent implements OnInit {
  adjustForm!: FormGroup;

  constructor(
    public dialogRef: MatDialogRef<StockAdjustDialogComponent>,
    @Inject(MAT_DIALOG_DATA) public data: StockAdjustDialogData,
    private fb: FormBuilder
  ) {}

  ngOnInit(): void {
    this.adjustForm = this.fb.group({
      type_mouvement: ['ajustement', [Validators.required]],
      quantite: ['', [Validators.required, Validators.min(1)]],
      motif: ['', [Validators.required]]
    });
  }

  onCancel(): void {
    this.dialogRef.close();
  }

  onSubmit(): void {
    if (this.adjustForm.valid) {
      this.dialogRef.close(this.adjustForm.value);
    }
  }
}
