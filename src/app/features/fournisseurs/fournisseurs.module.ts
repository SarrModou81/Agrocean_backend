import { NgModule } from '@angular/core';
import { SharedModule } from '../../shared/shared.module';
import { MatSelectModule } from '@angular/material/select';
import { MatMenuModule } from '@angular/material/menu';

import { FournisseursRoutingModule } from './fournisseurs-routing.module';
import { ListeComponent } from './liste/liste.component';
import { FormComponent } from './form/form.component';

@NgModule({
  declarations: [
    ListeComponent,
    FormComponent
  ],
  imports: [
    SharedModule,
    FournisseursRoutingModule,
    MatSelectModule,
    MatMenuModule
  ]
})
export class FournisseursModule { }
