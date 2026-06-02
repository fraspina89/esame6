/*
  NavbarComponent
  - Componente che mostra la barra di navigazione principale.
  - Si sottoscrive ad `AuthService` per aggiornare la UI in base allo stato di login
    (es. mostra link Test Auth solo se autenticato, mostra nome utente ed Esci).
  - Implementa `logout()` che cancella l'auth dal localStorage e notifica i subscribers.
*/
import { Component, OnInit } from '@angular/core';
import { Router } from '@angular/router';
import { AuthService } from 'src/app/_servizi/auth.service';
import { Auth } from 'src/app/_type/auth.type';

@Component({
  selector: 'navbar',
  templateUrl: './navbar.component.html',
  styleUrls: ['./navbar.component.scss']
})
export class NavbarComponent implements OnInit {

  auth: Auth = AuthService.auth
  queryRicerca = ''

  // Soglia in secondi entro cui mostrare l'avviso scadenza password (default: 15 giorni)
  private readonly AVVISO_GIORNI = 15 * 24 * 60 * 60

  constructor(private authService: AuthService, public router: Router) { }

  ngOnInit(): void {
    // Sottoscrizione al BehaviorSubject di AuthService per aggiornare la navbar
    // ogni volta che cambia lo stato di autenticazione
    this.authService.leggiObsAuth().subscribe((dati: Auth) => {
      this.auth = dati
    })
  }

  /**
   * Restituisce true se la password scade entro 15 giorni.
   * Usato nell'HTML per mostrare il banner di avviso.
   */
  get passwordInScadenza(): boolean {
    if (!this.auth.scadenzaPsw) return false
    const ora = Math.floor(Date.now() / 1000)
    return this.auth.scadenzaPsw - ora < this.AVVISO_GIORNI && this.auth.scadenzaPsw > ora
  }

  /**
   * Restituisce i giorni rimanenti alla scadenza della password.
   */
  get giorniAllaScadenza(): number {
    if (!this.auth.scadenzaPsw) return 0
    const ora = Math.floor(Date.now() / 1000)
    return Math.ceil((this.auth.scadenzaPsw - ora) / (24 * 60 * 60))
  }

  cercaFilm(): void {
    const q = this.queryRicerca.trim();
    if (!q) return;
    this.router.navigate(['/catalogo'], { queryParams: { search: q } });
    this.queryRicerca = '';
  }

  logout(): void {
    // Esegue logout centralizzato e reindirizza alla home 
    this.authService.logout()
    this.router.navigateByUrl('/', { replaceUrl: true })
  }
}
