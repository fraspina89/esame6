import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';
import { RouterModule } from '@angular/router';
import { FormsModule } from '@angular/forms';
import { NgbModalModule } from '@ng-bootstrap/ng-bootstrap';
import { EpisodioDettaglioComponent } from './episodio-dettaglio.component';

@NgModule({
  declarations: [EpisodioDettaglioComponent],
  imports: [
    CommonModule,
    FormsModule,
    NgbModalModule,
    RouterModule.forChild([
      { path: '', component: EpisodioDettaglioComponent }
    ])
  ]
})
export class EpisodioDettaglioModule { }
