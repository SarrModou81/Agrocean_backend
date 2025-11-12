import { NgModule } from '@angular/core';
import { SharedModule } from '../../shared/shared.module';

import { VentesRoutingModule } from './ventes-routing.module';
import { ListeComponent } from './liste/liste.component';


@NgModule({
  declarations: [
    ListeComponent
  ],
  imports: [
    SharedModule,
    VentesRoutingModule
  ]
})
export class VentesModule { }
