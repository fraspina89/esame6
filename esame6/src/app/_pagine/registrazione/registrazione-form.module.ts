import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';
import { ReactiveFormsModule, FormsModule } from '@angular/forms';
import { NgbTypeaheadModule, NgbDatepickerModule, NgbDropdownModule } from '@ng-bootstrap/ng-bootstrap';
import { RegistrazioneComponent } from './registrazione.component'

/**
 * Modulo che dichiara ed esporta RegistrazioneComponent senza routing.
 * Importato sia da RegistrazioneModule (per la rotta /registrazione)
 * sia da PrimaPaginaModule (per embedderlo nella home page).
 */
@NgModule({
  declarations: [RegistrazioneComponent],
  exports: [RegistrazioneComponent],
  imports: [
    CommonModule,
    ReactiveFormsModule,
    FormsModule,
    NgbDropdownModule,
    NgbTypeaheadModule,
    NgbDatepickerModule
  ]
})
export class RegistrazioneFormModule { }
