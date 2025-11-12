import { NgModule } from '@angular/core';
import { SharedModule } from '../../shared/shared.module';

import { StocksRoutingModule } from './stocks-routing.module';
import { ListeComponent } from './liste/liste.component';


@NgModule({
  declarations: [
    ListeComponent
  ],
  imports: [
    SharedModule,
    StocksRoutingModule
  ]
})
export class StocksModule { }
