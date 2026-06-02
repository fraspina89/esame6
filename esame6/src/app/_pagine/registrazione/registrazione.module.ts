import { NgModule } from '@angular/core';
import { RouterModule } from '@angular/router';
import { RegistrazioneComponent } from './registrazione.component';
import { RegistrazioneFormModule } from './registrazione-form.module';

@NgModule({
  imports: [
    RegistrazioneFormModule,
    RouterModule.forChild([
      { path: '', component: RegistrazioneComponent }
    ])
  ]
})
export class RegistrazioneModule { }
