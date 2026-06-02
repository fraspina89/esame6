import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';
import { RouterModule } from '@angular/router';
import { FormsModule, ReactiveFormsModule } from '@angular/forms';
import { NgbModalModule, NgbTooltipModule } from '@ng-bootstrap/ng-bootstrap';
import { ProfiloComponent } from './profilo.component';

@NgModule({
  declarations: [ProfiloComponent],
  imports: [
    CommonModule,
    FormsModule,
    ReactiveFormsModule,
    NgbModalModule,
    NgbTooltipModule,
    RouterModule.forChild([
      { path: '', component: ProfiloComponent }
    ])
  ]
})
export class ProfiloModule { }
