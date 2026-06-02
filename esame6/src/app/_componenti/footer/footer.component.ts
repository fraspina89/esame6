import { Component } from '@angular/core';

/**
 * FooterComponent
 * Componente del footer mostrato in fondo a ogni pagina.
 * 
 */
@Component({
  selector: 'app-footer',
  templateUrl: './footer.component.html',
  styleUrls: ['./footer.component.scss']
})
export class FooterComponent {
  // Anno corrente calcolato dinamicamente 
  anno: number = new Date().getFullYear()
}
