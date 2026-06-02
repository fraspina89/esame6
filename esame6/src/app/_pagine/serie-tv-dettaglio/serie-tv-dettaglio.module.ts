/*
  SerieTvDettaglioModule
  - Modulo lazy-loaded per la pagina di dettaglio serie TV (/serie-tv/:id).
  - Viene caricato solo quando l'utente naviga su quella route (risparmio bundle).
  - Importa FormsModule per il two-way binding [(ngModel)] nel form di modifica
    e NgbModalModule per aprire i modal NgBootstrap (modifica ed elimina).
*/
import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';
import { RouterModule, Routes } from '@angular/router';
import { FormsModule } from '@angular/forms';
import { NgbModalModule } from '@ng-bootstrap/ng-bootstrap';
import { SerieTvDettaglioComponent } from './serie-tv-dettaglio.component';

const routes: Routes = [
  { path: '', component: SerieTvDettaglioComponent }
];

@NgModule({
  declarations: [SerieTvDettaglioComponent],
  imports: [CommonModule, FormsModule, NgbModalModule, RouterModule.forChild(routes)]
})
export class SerieTvDettaglioModule {}

