import { NgModule } from '@angular/core';
import { SharedModule } from '../../shared/shared.module';

import { ClientsRoutingModule } from './clients-routing.module';
import { ListeComponent } from './liste/liste.component';
import { FormComponent } from './form/form.component';


@NgModule({
  declarations: [
    ListeComponent,
    FormComponent
  ],
  imports: [
    SharedModule,
    ClientsRoutingModule
  ]
})
export class ClientsModule { }
