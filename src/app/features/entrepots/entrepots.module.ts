import { NgModule } from '@angular/core';
import { SharedModule } from '../../shared/shared.module';
import { MatMenuModule } from '@angular/material/menu';

import { EntrepotsRoutingModule } from './entrepots-routing.module';
import { ListeComponent } from './liste/liste.component';
import { FormComponent } from './form/form.component';


@NgModule({
  declarations: [
    ListeComponent,
    FormComponent
  ],
  imports: [
    SharedModule,
    EntrepotsRoutingModule,
    MatMenuModule
  ]
})
export class EntrepotsModule { }
