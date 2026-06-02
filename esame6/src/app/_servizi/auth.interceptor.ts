/*
  AuthInterceptor
  - Intercettore HTTP che si aggancia automaticamente a OGNI chiamata HttpClient.
  - Se esiste un token JWT valido, lo aggiunge all'header `Authorization: Bearer <token>`.
  - Se la risposta del server restituisce 401 (non autorizzato) o 403 (vietato)
    e la richiesta conteneva il token, esegue il logout automatico e reindirizza
    l'utente al login con il `returnUrl` della pagina corrente.
  - Il flag `redirecting` evita redirect multipli in caso di chiamate parallele che
    ricevono tutte 401/403 contemporaneamente.
  Registrato in AppModule con: { provide: HTTP_INTERCEPTORS, useClass: AuthInterceptor, multi: true }
*/
import { Injectable } from '@angular/core';
import { HttpInterceptor, HttpRequest, HttpHandler, HttpEvent, HttpErrorResponse } from '@angular/common/http';
import { Observable, throwError } from 'rxjs';
import { catchError } from 'rxjs/operators';
import { Router } from '@angular/router';
import { AuthService } from './auth.service';

@Injectable()
export class AuthInterceptor implements HttpInterceptor {
  // Flag per evitare redirect multipli se più richieste ricevono 401/403 in parallelo
  private redirecting: boolean = false

  constructor(private router: Router, private authService: AuthService) { }

  intercept(req: HttpRequest<any>, next: HttpHandler): Observable<HttpEvent<any>> {
    // Aggiunge automaticamente header Authorization se esiste un token
    const token = this.authService.getToken()
    let authReq = req
    if (token) {
      authReq = req.clone({ setHeaders: { Authorization: 'Bearer ' + token } })
    }

    // Indica se la richiesta conteneva l'header Authorization (usato per decidere se redirectare)
    const hadAuthHeader = !!authReq.headers.get('Authorization')

    return next.handle(authReq).pipe(
      catchError((err: HttpErrorResponse) => {
        // Se riceviamo 401 o 403 puliamo lo stato di autenticazione e reindirizziamo al login
        if ((err.status === 401 || err.status === 403) && !this.redirecting && hadAuthHeader) {
          this.redirecting = true
          try {
            // Usa la API pubblica di logout per coerenza
            this.authService.logout()
          } catch (e) {
            // ignore
          }

          // Evita redirect se siamo già nella pagina di login
          const current = this.router.url || '/'
          if (!current.startsWith('/login')) {
            this.router.navigate(['/login'], { queryParams: { returnUrl: current } })
          }

          // Reset flag dopo breve timeout per permettere eventuali future redirect
          setTimeout(() => this.redirecting = false, 1000)
        }

        return throwError(() => err)
      })
    )
  }
}
