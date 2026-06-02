import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';
import { RouterModule } from '@angular/router';
import { FormsModule } from '@angular/forms';
import { PrimaPaginaComponent } from './prima-pagina.component';
import { RegistrazioneFormModule } from '../registrazione/registrazione-form.module';

@NgModule({
  declarations: [PrimaPaginaComponent],
  imports: [
    CommonModule,
    FormsModule,
    RegistrazioneFormModule,
    RouterModule.forChild([
      { path: '', component: PrimaPaginaComponent }
    ])
  ]
})
export class PrimaPaginaModule { }
