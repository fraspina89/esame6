import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';
import { RouterModule } from '@angular/router';
import { FormsModule } from '@angular/forms';
import { NgbModalModule } from '@ng-bootstrap/ng-bootstrap';
import { CatalogoComponent } from './catalogo.component';

@NgModule({
  declarations: [CatalogoComponent],
  imports: [
    CommonModule,
    FormsModule,
    NgbModalModule,
    RouterModule.forChild([
      { path: '', component: CatalogoComponent }
    ])
  ]
})
export class CatalogoModule { }
