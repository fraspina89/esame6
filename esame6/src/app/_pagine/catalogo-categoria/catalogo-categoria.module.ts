import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';
import { RouterModule } from '@angular/router';
import { CatalogoCategoriaComponent } from './catalogo-categoria.component';

@NgModule({
  declarations: [CatalogoCategoriaComponent],
  imports: [
    CommonModule,
    RouterModule.forChild([
      { path: '', component: CatalogoCategoriaComponent }
    ])
  ]
})
export class CatalogoCategoriaModule { }
