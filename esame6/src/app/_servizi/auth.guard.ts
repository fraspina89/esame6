/*
  AuthGuard
  - Guard di Angular che protegge le rotte che richiedono autenticazione.
  - Implementa sia `CanActivate` (blocca l'accesso alla rotta) sia `CanLoad`
    (blocca il download del modulo lazy se non autenticato).
  - In entrambi i casi reindirizza al login con il parametro `returnUrl`
    in modo che dopo il login l'utente torni alla pagina che stava cercando.
*/
import { Injectable } from '@angular/core';
import { CanActivate, CanLoad, ActivatedRouteSnapshot, RouterStateSnapshot, UrlTree, Router, Route, UrlSegment } from '@angular/router';
import { Observable } from 'rxjs';
import { AuthService } from './auth.service';

@Injectable({ providedIn: 'root' })
export class AuthGuard implements CanActivate, CanLoad {

  constructor(private authService: AuthService, private router: Router) { }

  canActivate(
    route: ActivatedRouteSnapshot,
    state: RouterStateSnapshot
  ): Observable<boolean | UrlTree> | Promise<boolean | UrlTree> | boolean | UrlTree {
    // Se l'utente è autenticato (token valido) permette l'accesso
    if (this.authService.isAuthenticated()) {
      return true
    }

    // Altrimenti reindirizza al login con returnUrl
    this.router.navigate(['/login'], { queryParams: { returnUrl: state.url } })
    return false
  }

  // CanLoad impedisce il caricamento del modulo lazy se non autenticato
  canLoad(route: Route, segments: UrlSegment[]): Observable<boolean> | Promise<boolean> | boolean {
    if (this.authService.isAuthenticated()) return true
    // Ricostruisci una returnUrl semplice dalle segments
    const returnUrl = '/' + (segments.map(s => s.path).join('/'))
    this.router.navigate(['/login'], { queryParams: { returnUrl } })
    return false
  }
}
