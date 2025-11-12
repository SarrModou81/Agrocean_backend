import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';

import { CommandesAchatRoutingModule } from './commandes-achat-routing.module';
import { ListeComponent } from './liste/liste.component';
import { FormComponent } from './form/form.component';
import { SharedModule } from '../../shared/shared.module';
import { MatSelectModule } from '@angular/material/select';
import { MatMenuModule } from '@angular/material/menu';
import { MatDatepickerModule } from '@angular/material/datepicker';
import { MatNativeDateModule } from '@angular/material/core';
import { MatAutocompleteModule } from '@angular/material/autocomplete';

@NgModule({
  declarations: [
    ListeComponent,
    FormComponent
  ],
  imports: [
    CommonModule,
    CommandesAchatRoutingModule,
    SharedModule,
    MatSelectModule,
    MatMenuModule,
    MatDatepickerModule,
    MatNativeDateModule,
    MatAutocompleteModule
  ]
})
export class CommandesAchatModule { }
