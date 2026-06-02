/*
  ProfiloComponent
  - Mostra i dati del profilo dell'utente autenticato.
  - Permette la modifica di nome, cognome e password (opzionale).
  - Per gli amministratori espone la dashboard di Gestione Utenti:
      * tabella con tutti i contatti
      * modal per creare un nuovo utente
      * modal per modificare nome/cognome/stato di un utente esistente
      * modal di conferma per l'eliminazione
      * notifica a scomparsa (4 s) dopo ogni operazione
  - Usa NgbModal (ng-bootstrap) per tutte le finestre modali.
  - Il pattern password è identico al form di registrazione:
      min 8 caratteri, almeno 1 maiuscola, almeno 1 carattere speciale.
*/
import { Component, OnDestroy, OnInit, TemplateRef } from '@angular/core';
import { FormBuilder, FormGroup, Validators } from '@angular/forms';
import { Router } from '@angular/router';
import { Subject, of } from 'rxjs';
import { switchMap } from 'rxjs/operators';
import { takeUntil } from 'rxjs/operators';
import { NgbModal, NgbModalRef } from '@ng-bootstrap/ng-bootstrap';
import { ApiService } from '../../_servizi/api.service';
import { AuthService } from '../../_servizi/auth.service';
import { UtilityService } from '../../_servizi/utility.service';
import { IContatto } from '../../_models';
import { Auth } from '../../_type/auth.type';

@Component({
  selector: 'app-profilo',
  templateUrl: './profilo.component.html',
  styleUrls: ['./profilo.component.scss']
})
export class ProfiloComponent implements OnInit, OnDestroy {

  auth: Auth = AuthService.auth;
  contatto: IContatto | null = null;
  caricamento = true;
  errore = '';

  // scadenza token (da JWT)
  tokenExp: Date | null = null;

  // lista utenti (solo admin)
  utenti: IContatto[] = [];
  caricamentoUtenti = false;
  erroreUtenti = '';

  // form modifica dati personali
  formModifica: FormGroup;
  formModificaCaricamento = false;
  formModificaErrore = '';
  formModificaSuccesso = '';
  modalModificaRef: NgbModalRef | null = null;

  // utente selezionato per elimina
  utenteSelezionato: IContatto | null = null;
  eliminaCaricamento = false;
  modalEliminaRef: NgbModalRef | null = null;

  // utente selezionato per modifica admin
  formModificaAdmin: FormGroup;
  formModificaAdminCaricamento = false;
  formModificaAdminErrore = '';
  modalModificaAdminRef: NgbModalRef | null = null;

  // notifica dashboard (modifica/elimina/crea utente)
  notificaDashboard = '';
  notificaDashboardTipo: 'success' | 'danger' = 'success';
  private notificaTimer: any = null;

  private destroy$ = new Subject<void>();

  constructor(
    private api: ApiService,
    private authService: AuthService,
    private fb: FormBuilder,
    private modal: NgbModal,
    private router: Router
  ) {
    // Form modifica dati personali: nome/cognome obbligatori; cambio password opzionale
    // (stesse regole del form di registrazione: min 8 car., 1 maiuscola, 1 carattere speciale)
    this.formModifica = this.fb.group({
      nome: ['', [Validators.required, Validators.minLength(2), Validators.maxLength(50)]],
      cognome: ['', [Validators.required, Validators.minLength(2), Validators.maxLength(50)]],
      passwordAttuale: [''],
      nuovaPassword: ['', [Validators.minLength(8), Validators.pattern(/^(?=.*[A-Z])(?=.*[!@#$%^&*()_+\-=\[\]{};':"\\|,.<>\/?]).+$/)]]
    });

    this.formModificaAdmin = this.fb.group({
      nome: ['', [Validators.required, Validators.minLength(2)]],
      cognome: ['', [Validators.required, Validators.minLength(2)]],
      idContattoStato: [1]
    });
  }

  ngOnInit(): void {
    this.authService.leggiObsAuth().pipe(takeUntil(this.destroy$)).subscribe((a: Auth) => {
      this.auth = a;
    });

    // decodifica scadenza token
    if (this.auth.tk) {
      try {
        const payload = UtilityService.leggiToken(this.auth.tk);
        if (payload?.exp) {
          this.tokenExp = new Date(payload.exp * 1000);
        }
      } catch (_) {}
    }

    this.caricaDatiProfilo();

    if (this.isAdmin) {
      this.caricaUtenti();
    }
  }

  ngOnDestroy(): void {
    this.destroy$.next();
    this.modal.dismissAll();
  }

  get isAdmin(): boolean {
    return this.auth.idRuolo === 1;
  }

  get nomeRuolo(): string {
    return this.isAdmin ? 'Amministratore' : 'Utente';
  }

  get nomeStato(): string {
    switch (this.auth.idStato) {
      case 1: return 'Attivo';
      case 2: return 'Sospeso';
      case 3: return 'Eliminato';
      default: return 'Sconosciuto';
    }
  }

  private caricaDatiProfilo(): void {
    if (!this.auth.idUtente) { this.caricamento = false; return; }
    this.api.getContatti(this.auth.idUtente).pipe(takeUntil(this.destroy$)).subscribe({
      next: (rit) => {
        this.contatto = rit.data as IContatto;
        this.caricamento = false;
      },
      error: () => {
        this.errore = 'Impossibile caricare i dati del profilo.';
        this.caricamento = false;
      }
    });
  }

  caricaUtenti(): void {
    this.caricamentoUtenti = true;
    // Richiede tutti gli utenti senza limite di paginazione (per_page=9999)
    this.api.getContatti(null, 9999).pipe(takeUntil(this.destroy$)).subscribe({
      next: (rit) => {
        this.utenti = (rit.data as any)?.data ?? (rit.data as IContatto[]) ?? [];
        this.caricamentoUtenti = false;
      },
      error: () => {
        this.erroreUtenti = 'Impossibile caricare la lista utenti.';
        this.caricamentoUtenti = false;
      }
    });
  }

  // === MODIFICA PROFILO PERSONALE ===

  apriModifica(tpl: TemplateRef<any>): void {
    this.formModificaErrore = '';
    this.formModificaSuccesso = '';
    this.formModifica.patchValue({
      nome: this.contatto?.nome ?? '',
      cognome: this.contatto?.cognome ?? '',
      passwordAttuale: '',
      nuovaPassword: ''
    });
    this.modalModificaRef = this.modal.open(tpl, { centered: true, size: 'md' });
  }

  salvaModifica(): void {
    const val = this.formModifica.value;
    if (!this.formModifica.get('nome')!.valid || !this.formModifica.get('cognome')!.valid || !this.auth.idUtente) return;

    const vuoleCambiarePsw = !!(val.nuovaPassword?.trim());

    // Se l'utente ha compilato la nuova password, la password attuale è obbligatoria
    if (vuoleCambiarePsw && !val.passwordAttuale?.trim()) {
      this.formModificaErrore = 'Inserisci la password attuale per poterla cambiare.';
      return;
    }

    // Se la nuova password è compilata ma non supera la validazione, blocca
    if (vuoleCambiarePsw && this.formModifica.get('nuovaPassword')!.invalid) return;

    this.formModificaCaricamento = true;
    this.formModificaErrore = '';

    // Prima aggiorna nome/cognome, poi (se richiesto) cambia la password
    this.api.modificaContatto(this.auth.idUtente, { nome: val.nome, cognome: val.cognome })
      .pipe(
        takeUntil(this.destroy$),
        switchMap(() => {
          if (vuoleCambiarePsw) {
            // Il backend riceve le password in chiaro e calcola SHA512 internamente.
            // Dopo il cambio invalida tutte le sessioni → forza il logout.
            return this.api.cambiaPassword(this.auth.idUtente!, val.passwordAttuale, val.nuovaPassword);
          }
          // Nessun cambio password: ritorna un observable che emette null e completa
          return of(null);
        })
      )
      .subscribe({
        next: () => {
          this.formModificaCaricamento = false;
          if (vuoleCambiarePsw) {
            // Il backend ha già invalidato la sessione: avvisa e fa logout
            this.formModificaSuccesso = 'Dati aggiornati. Verrai disconnesso per il nuovo login con la nuova password.';
            setTimeout(() => {
              this.authService.logout();
              this.router.navigate(['/login']);
            }, 2500);
          } else {
            this.formModificaSuccesso = 'Dati aggiornati con successo.';
            this.caricaDatiProfilo();
            setTimeout(() => this.modalModificaRef?.close(), 1200);
          }
        },
        error: (err) => {
          const msg: string = err?.message ?? '';
          if (msg.includes('ERR_CURRENT_PASSWORD')) {
            this.formModificaErrore = 'Password attuale errata.';
          } else if (msg.includes('già utilizzata')) {
            this.formModificaErrore = 'Nuova password già utilizzata recentemente.';
          } else {
            this.formModificaErrore = 'Errore durante l\'aggiornamento.';
          }
          this.formModificaCaricamento = false;
        }
      });
  }

  // === MODIFICA UTENTE (ADMIN) ===

  apriModificaAdmin(utente: IContatto, tpl: TemplateRef<any>): void {
    this.utenteSelezionato = utente;
    this.formModificaAdminErrore = '';
    this.formModificaAdmin.patchValue({
      nome: utente.nome ?? '',
      cognome: utente.cognome ?? '',
      idContattoStato: utente.idContattoStato ?? 1
    });
    this.modalModificaAdminRef = this.modal.open(tpl, { centered: true, size: 'md' });
  }

  salvaModificaAdmin(): void {
    if (this.formModificaAdmin.invalid || !this.utenteSelezionato) return;
    this.formModificaAdminCaricamento = true;
    this.api.modificaContatto(this.utenteSelezionato.idContatto, this.formModificaAdmin.value)
      .pipe(takeUntil(this.destroy$))
      .subscribe({
        next: () => {
          this.formModificaAdminCaricamento = false;
          this.modalModificaAdminRef?.close();
          this.caricaUtenti();
          this.mostraNotificaDashboard('Utente modificato con successo.', 'success');
        },
        error: () => {
          this.formModificaAdminErrore = 'Errore durante la modifica.';
          this.formModificaAdminCaricamento = false;
        }
      });
  }

  // === ELIMINA UTENTE (ADMIN) ===

  apriElimina(utente: IContatto, tpl: TemplateRef<any>): void {
    this.utenteSelezionato = utente;
    this.eliminaCaricamento = false;
    this.modalEliminaRef = this.modal.open(tpl, { centered: true, size: 'sm' });
  }

  confermaElimina(): void {
    if (!this.utenteSelezionato) return;
    this.eliminaCaricamento = true;
    const nomeEliminato = this.utenteSelezionato.nomeCompleto;
    this.api.eliminaContatto(this.utenteSelezionato.idContatto)
      .pipe(takeUntil(this.destroy$))
      .subscribe({
        next: () => {
          this.eliminaCaricamento = false;
          this.modalEliminaRef?.close();
          this.caricaUtenti();
          this.mostraNotificaDashboard(`Utente "${nomeEliminato}" eliminato.`, 'danger');
        },
        error: () => { this.eliminaCaricamento = false; }
      });
  }

  // Mostra un banner colorato sopra la tabella utenti dopo crea/modifica/elimina.
  // Scompare automaticamente dopo 4 secondi.
  private mostraNotificaDashboard(messaggio: string, tipo: 'success' | 'danger'): void {
    if (this.notificaTimer) clearTimeout(this.notificaTimer);
    this.notificaDashboard = messaggio;
    this.notificaDashboardTipo = tipo;
    this.notificaTimer = setTimeout(() => { this.notificaDashboard = ''; }, 4000);
  }
}
