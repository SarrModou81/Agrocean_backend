import { NgModule } from '@angular/core';
import { SharedModule } from '../../shared/shared.module';

import { AchatsRoutingModule } from './achats-routing.module';
import { ListeComponent } from './liste/liste.component';
import { FormComponent } from './form/form.component';


@NgModule({
  declarations: [
    ListeComponent,
    FormComponent
  ],
  imports: [
    SharedModule,
    AchatsRoutingModule
  ]
})
export class AchatsModule { }
