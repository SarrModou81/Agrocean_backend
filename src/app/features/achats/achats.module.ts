import { NgModule } from '@angular/core';
import { SharedModule } from '../../shared/shared.module';

import { AchatsRoutingModule } from './achats-routing.module';
import { ListeComponent } from './liste/liste.component';


@NgModule({
  declarations: [
    ListeComponent
  ],
  imports: [
    SharedModule,
    AchatsRoutingModule
  ]
})
export class AchatsModule { }
