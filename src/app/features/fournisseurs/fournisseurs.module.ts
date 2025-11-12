import { NgModule } from '@angular/core';
import { SharedModule } from '../../shared/shared.module';

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
    FournisseursRoutingModule
  ]
})
export class FournisseursModule { }
