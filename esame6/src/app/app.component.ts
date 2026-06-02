/*
  AppComponent
  - Componente radice dell'app, montato nel file index.html tramite il selettore <root>.
  - Ha due responsabilità principali:
    1. GUARD ROTTE: ascolta ogni NavigationStart dal Router e reindirizza al login
       se l'utente non è autenticato e cerca di accedere a una rotta protetta.
    2. GUARD POPSTATE (back/forward del browser): ascolta l'evento nativo `popstate`
       e fa logout automatico se un utente autenticato torna su una pagina pubblica
       premendo il tasto back.
*/
import { Component, NgZone, OnInit, OnDestroy } from '@angular/core';
import { Router, NavigationStart } from '@angular/router';
import { PUBLIC_ROUTES } from './app-routing.module';
import { Subscription } from 'rxjs';
import { AuthService } from './_servizi/auth.service';

@Component({
  selector: 'root',
  templateUrl: './app.component.html',
  styleUrls: ['./app.component.scss']
})
export class AppComponent implements OnInit, OnDestroy {
  title = 'esame6';

  private routerSub: Subscription | null = null

  // Arrow function: mantiene il riferimento a `this` quando passata a window.addEventListener
  private popStateHandler = (ev: PopStateEvent) => {
    // ngZone.run() è indispensabile: popstate è un evento nativo (fuori dalla Angular zone),
    // quindi senza di esso obsAuth$.next() non triggera il change detection nella navbar.
    this.ngZone.run(() => {
      if (!this.authService.isAuthenticated()) return
      const targetPath = window.location.pathname
      const isPublic = PUBLIC_ROUTES.some(p =>
        targetPath === p || targetPath.startsWith(p + '/') ||
        targetPath.startsWith(p + '?') || targetPath.startsWith(p + '#')
      )
      if (isPublic) {
        // L'utente autenticato torna su una pagina pubblica col tasto back:
        // logout subito (aggiorna la navbar) e redirect a /login
        this.authService.logout()
        this.router.navigate(['/login'], { replaceUrl: true })
      }
    })
  }

  constructor(private router: Router, private authService: AuthService, private ngZone: NgZone) {}

  ngOnInit(): void {
    // Guard: se non autenticato su rotta protetta, redirect a /login
    this.routerSub = this.router.events.subscribe(e => {
      if (e instanceof NavigationStart) {
        const ok = this.authService.isAuthenticated()
        const isPublic = PUBLIC_ROUTES.some(p => e.url === p || e.url.startsWith(p + '/') || e.url.startsWith(p + '?') || e.url.startsWith(p + '#'))
        if (!ok && !isPublic) {
          this.router.navigate(['/login'], { queryParams: { returnUrl: e.url } })
        }
      }
    })

    // Intercetta il popstate (back/forward) e verifica token
    window.addEventListener('popstate', this.popStateHandler)
  }

  ngOnDestroy(): void {
    if (this.routerSub) this.routerSub.unsubscribe()
    window.removeEventListener('popstate', this.popStateHandler)
  }
}
