import { NgModule } from '@angular/core';
import { RouterModule, Routes } from '@angular/router';
import { ListeComponent } from './liste/liste.component';
import { FormComponent } from './form/form.component';

const routes: Routes = [
  { path: '', component: ListeComponent },
  { path: 'nouveau', component: FormComponent },
  { path: 'edit/:id', component: FormComponent }
];

@NgModule({
  imports: [RouterModule.forChild(routes)],
  exports: [RouterModule]
})
export class ProduitsRoutingModule { }
