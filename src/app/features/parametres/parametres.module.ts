import { NgModule } from '@angular/core';
import { SharedModule } from '../../shared/shared.module';
import { MatSelectModule } from '@angular/material/select';
import { MatSlideToggleModule } from '@angular/material/slide-toggle';
import { MatDividerModule } from '@angular/material/divider';

import { ParametresRoutingModule } from './parametres-routing.module';
import { SettingsComponent } from './settings/settings.component';


@NgModule({
  declarations: [
    SettingsComponent
  ],
  imports: [
    SharedModule,
    ParametresRoutingModule,
    MatSelectModule,
    MatSlideToggleModule,
    MatDividerModule
  ]
})
export class ParametresModule { }
