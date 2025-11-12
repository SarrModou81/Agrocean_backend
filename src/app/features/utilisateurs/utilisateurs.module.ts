import { NgModule } from '@angular/core';
import { SharedModule } from '../../shared/shared.module';
import { MatSelectModule } from '@angular/material/select';
import { MatMenuModule } from '@angular/material/menu';
import { MatChipsModule } from '@angular/material/chips';

import { UtilisateursRoutingModule } from './utilisateurs-routing.module';
import { ListeComponent } from './liste/liste.component';
import { FormComponent } from './form/form.component';


@NgModule({
  declarations: [
    ListeComponent,
    FormComponent
  ],
  imports: [
    SharedModule,
    UtilisateursRoutingModule,
    MatSelectModule,
    MatMenuModule,
    MatChipsModule
  ]
})
export class UtilisateursModule { }
