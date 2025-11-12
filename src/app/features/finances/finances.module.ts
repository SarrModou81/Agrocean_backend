import { NgModule } from '@angular/core';
import { SharedModule } from '../../shared/shared.module';
import { MatSelectModule } from '@angular/material/select';

import { FinancesRoutingModule } from './finances-routing.module';
import { DashboardComponent } from './dashboard/dashboard.component';


@NgModule({
  declarations: [
    DashboardComponent
  ],
  imports: [
    SharedModule,
    FinancesRoutingModule,
    MatSelectModule
  ]
})
export class FinancesModule { }
