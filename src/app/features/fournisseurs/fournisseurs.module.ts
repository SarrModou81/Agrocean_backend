import { NgModule } from '@angular/core';
import { SharedModule } from '../../shared/shared.module';

import { FournisseursRoutingModule } from './fournisseurs-routing.module';
import { ListeComponent } from './liste/liste.component';


@NgModule({
  declarations: [
    ListeComponent
  ],
  imports: [
    SharedModule,
    FournisseursRoutingModule
  ]
})
export class FournisseursModule { }
