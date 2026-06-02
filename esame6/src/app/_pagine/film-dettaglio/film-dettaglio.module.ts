import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';
import { RouterModule } from '@angular/router';
import { FormsModule } from '@angular/forms';
import { NgbModalModule } from '@ng-bootstrap/ng-bootstrap';
import { FilmDettaglioComponent } from './film-dettaglio.component';

@NgModule({
  declarations: [FilmDettaglioComponent],
  imports: [
    CommonModule,
    FormsModule,
    NgbModalModule,
    RouterModule.forChild([
      { path: '', component: FilmDettaglioComponent }
    ])
  ]
})
export class FilmDettaglioModule { }
