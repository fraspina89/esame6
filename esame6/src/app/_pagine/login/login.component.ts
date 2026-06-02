/*
  LoginComponent
  - Gestisce il form di login (Reactive Forms) e la procedura di accesso.
  - Usa `ApiService.login` che implementa la login in 2 fasi verso il backend.
  - Se il login ha successo estrae i dati dal token (con UtilityService.leggiToken),
    popola `Auth` e aggiorna `AuthService` + `localStorage`.
*/
import { Component, OnDestroy, OnInit } from '@angular/core';
import { FormBuilder, FormGroup, Validators } from '@angular/forms';
import { Router } from '@angular/router';
import { catchError, delay, Observable, Observer, of, Subject, take, takeUntil } from 'rxjs';
import { IRrispostaServer } from 'src/app/_interfacce/IRispostaServer.interface';
import { ApiService } from 'src/app/_servizi/api.service';
import { AuthService } from 'src/app/_servizi/auth.service';
import { UtilityService } from 'src/app/_servizi/utility.service';
import { Auth } from 'src/app/_type/auth.type';

@Component({
  selector: 'login',
  templateUrl: './login.component.html',
  styleUrls: ['./login.component.scss']
})
export class LoginComponent implements OnInit, OnDestroy {

  stoControllando: boolean = false
  reactiveForm: FormGroup
  auth: Auth = AuthService.auth
  errore: string | null = null

  private distruggi$ = new Subject<void>()

  constructor(
    private fb: FormBuilder,
    private authService: AuthService,
    private api: ApiService,
    private router: Router
  ) {
    this.reactiveForm = this.fb.group({
      'utente': ['', [Validators.required, Validators.email, Validators.minLength(5), Validators.maxLength(40)]],
      'password': ['', [Validators.required, Validators.minLength(6), Validators.maxLength(20)]]
    })
  }

  ngOnInit(): void {
    // Se l'utente arriva su /login mentre è già autenticato (es. tasto back), esegui logout
    if (this.authService.isAuthenticated()) {
      this.authService.logout()
    }
    this.authService.leggiObsAuth().subscribe((dati: Auth) => {
      this.auth = dati
    })
  }

  ngOnDestroy(): void {
    this.distruggi$.next()
  }

  accedi(): void {
    if (this.reactiveForm.invalid) return

    const utente: string = this.reactiveForm.controls['utente'].value
    const password: string = this.reactiveForm.controls['password'].value
    this.stoControllando = true
    this.errore = null
    // Avvia l'osservabile di login (che esegue la fase1 + fase2)
    this.obsLogin(utente, password).subscribe(this.osservoLogin())
  }

  private obsLogin(utente: string, password: string): Observable<IRrispostaServer> {
    return this.api.login(utente, password).pipe(
      delay(1000),
      take(1),
      catchError((err) => {
        const risposta: IRrispostaServer = { data: null, message: null, error: err }
        return of(risposta)
      }),
      takeUntil(this.distruggi$)
    )
  }

  private osservoLogin(): Observer<IRrispostaServer> {
    return {
      next: (rit: IRrispostaServer) => {
        // Se la chiamata restituisce dati e non errori, login riuscito
        if (rit.data !== null && rit.error == null) {
          const tk: string = rit.data.tk

          // Decodifica il token per estrarre i campi necessari a `Auth`
          const contenutoToken = UtilityService.leggiToken(tk)

          // Costruisce l'oggetto Auth usato dall'app (nome, ruolo, id, abilita)
          const auth: Auth = {
            idLingua: 1,
            tk: tk,
            nome: contenutoToken.data.nome,
            idRuolo: contenutoToken.data.idContattoRuolo,
            idStato: contenutoToken.data.idContattoStato,
            idUtente: contenutoToken.data.idContatto,
            abilita: contenutoToken.data.abilita,
            // Scadenza password inserita nel token dal server 
            scadenzaPsw: contenutoToken.data.scadenza_psw ?? null
          }

          // Aggiorna lo stato globale e salva su localStorage
          this.authService.settaObsAuth(auth)
          this.authService.scriviAuthSuLocalStorage(auth)

          // Naviga al catalogo dopo login (replaceUrl:true impedisce di tornare su /login col back)
          this.router.navigate(['/catalogo'], { replaceUrl: true })
        } else {
          // Mostra messaggio di errore per credenziali non valide
          this.errore = "Credenziali non valide"
        }

        // Fine controllo, disabilita spinner
        this.stoControllando = false
      },
      error: (err) => {
        this.errore = "Errore di connessione"
        this.stoControllando = false
      },
      complete: () => {
        this.stoControllando = false
      }
    }
  }
}
