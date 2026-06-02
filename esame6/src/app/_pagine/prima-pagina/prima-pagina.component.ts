import { Component, OnInit } from '@angular/core';
import { Router } from '@angular/router';
import { AuthService } from 'src/app/_servizi/auth.service';

/**
 * PrimaPaginaComponent
 * Pagina pubblica di atterraggio (home page).
 * Mostra un form email iniziale: l'utente inserisce l'email
 * e clicca un bottone per avviare la registrazione.
 * - Se l'email non è valida viene mostrato un errore.
 * - Se l'email è valida, `mostraForm` diventa true e il form
 *   completo di registrazione viene visualizzato.
 * - Se l'utente arriva qui mentre è già autenticato (es. tasto back dal catalogo),
 *   viene fatto logout automatico e reindirizzato a /login.
 */
@Component({
  selector: 'app-prima-pagina',
  templateUrl: './prima-pagina.component.html',
  styleUrls: ['./prima-pagina.component.scss']
})
export class PrimaPaginaComponent implements OnInit {
  // Controlla se mostrare il form completo di registrazione
  mostraForm: boolean = false
  // Valore dell'input email nella home
  emailHome: string = ''
  // Flag per mostrare il messaggio di errore email non valida
  emailError: boolean = false

  constructor(private authService: AuthService, private router: Router) { }

  ngOnInit(): void {
    // Se l'utente arriva sulla home mentre è già autenticato (es. tasto back dal catalogo),
    // esegui logout e reindirizza a /login (replaceUrl evita di tornare qui con back)
    if (this.authService.isAuthenticated()) {
      this.authService.logout()
      this.router.navigate(['/login'], { replaceUrl: true })
    }
  }

  avviaRegistrazione(): void {
    // Regex basilare per validare il formato email prima di procedere
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/
    if (!this.emailHome || !emailRegex.test(this.emailHome.trim())) {
      this.emailError = true
      return
    }
    this.emailError = false
    // Email valida: mostra il form di registrazione completo
    this.mostraForm = true
  }
}
