import { NgModule } from '@angular/core';
import { SharedModule } from '../../shared/shared.module';
import { MatSelectModule } from '@angular/material/select';
import { MatChipsModule } from '@angular/material/chips';
import { MatMenuModule } from '@angular/material/menu';

import { ProduitsRoutingModule } from './produits-routing.module';
import { ListeComponent } from './liste/liste.component';
import { FormComponent } from './form/form.component';


@NgModule({
  declarations: [
    ListeComponent,
    FormComponent
  ],
  imports: [
    SharedModule,
    ProduitsRoutingModule,
    MatSelectModule,
    MatChipsModule,
    MatMenuModule
  ]
})
export class ProduitsModule { }
