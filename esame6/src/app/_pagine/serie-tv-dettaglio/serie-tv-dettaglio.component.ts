/*
  SerieTvDettaglioComponent
  - Pagina di dettaglio di una singola serie TV, raggiungibile tramite /serie-tv/:id.
  - Funzionalità:
    * Carica la serie TV dal backend in base all'ID presente nell'URL.
    * Mostra titolo, descrizione, anni, stagioni, regista, attori, locandina e video.
    * Organizza gli episodi per stagione in pannelli accordion click-to-expand.
    * Permette la riproduzione del video della serie cliccando su "Riproduci".
    * Solo agli Amministratori (idRuolo === 1) mostra i bottoni Modifica ed Elimina.
    * Tramite modal NgBootstrap:
      - Modifica: precarica i campi con i dati attuali e salva via POST + _method=PUT
        (method spoofing: PHP non supporta $_FILES con richieste PUT native).
*/
import { Component, OnInit, OnDestroy, TemplateRef } from '@angular/core';
import { ActivatedRoute, Router } from '@angular/router';
import { Subject } from 'rxjs';
import { takeUntil } from 'rxjs/operators';
import { NgbModal, NgbModalRef } from '@ng-bootstrap/ng-bootstrap';
import { ApiService } from '../../_servizi/api.service';
import { AuthService } from '../../_servizi/auth.service';
import { ISerie, IEpisodio, ICategoria } from '../../_models';
import { Auth } from '../../_type/auth.type';
import { environment } from '../../../environments/environment';

@Component({
  selector: 'app-serie-tv-dettaglio',
  templateUrl: './serie-tv-dettaglio.component.html',
  styleUrls: ['./serie-tv-dettaglio.component.scss']
})
export class SerieTvDettaglioComponent implements OnInit, OnDestroy {

  readonly imgFallback = environment.locandineBase + '/elenco.jpg';

  // ===== Stato generale =====
  serie: ISerie | null = null;      // dati della serie TV corrente
  caricamento = true;               // true mentre si attendono i dati dal server
  errore = '';                      // messaggio di errore se la serie non viene trovata
  auth: Auth = AuthService.auth;    // stato autenticazione (aggiornato tramite subscribe)
  videoAperto = false;              // true quando il player video principale è visibile

  // ===== Accordion episodi =====
  videoApertoEpisodio: number | null = null; // idEpisodio il cui player è aperto (null = nessuno)

  // ===== Stagioni (costruite dall'array serie.episodi) =====
  stagioni: Map<number, IEpisodio[]> = new Map(); // mappa numeroStagione → lista episodi
  stagionNumbers: number[] = [];                   // numeri di stagione ordinati
  stagioneAperta: number | null = null;            // numero della stagione aperta nell'accordion

  // ===== Categorie per il dropdown del form modifica =====
  categorie: ICategoria[] = [];

  // ===== Campi del form "Modifica Serie TV" =====
  formNome           = '';
  formIdCategoria    = 0;            // 0 = nessuna categoria selezionata
  formDescrizione    = '';
  formTotaleStagioni: number | null = null;
  formRegista        = '';
  formAttori         = '';
  formAnnoInizio: number | null = null;
  formAnnoFine: number | null   = null;
  formCaricamento    = false;        // true durante l'invio della richiesta al server
  formErrore         = '';           // messaggio di errore del form modifica
  formMessaggio      = '';           // messaggio di successo del form modifica
  formLocandina: File | null = null; // nuovo file locandina (null = mantieni quello esistente)
  formCarousel: File | null  = null; // nuovo file carousel
  formVideo: File | null     = null; // nuovo file video

  private modalModificaRef: NgbModalRef | null = null;
  private modalEliminaRef: NgbModalRef | null = null;

  // Subject per cancellare tutte le sottoscrizioni RxJS quando il componente viene distrutto
  private destroy$ = new Subject<void>();

  constructor(
    private apiService: ApiService,
    private route: ActivatedRoute,
    private router: Router,
    private authService: AuthService,
    private modalService: NgbModal
  ) {}

  ngOnInit(): void {
    window.scrollTo(0, 0);

    // Aggiorna lo stato auth reattivamente (es. logout mentre si è sulla pagina)
    this.authService.leggiObsAuth()
      .pipe(takeUntil(this.destroy$))
      .subscribe((a: Auth) => this.auth = a);

    // Carica le categorie per il dropdown del form di modifica
    this.apiService.getCategorie()
      .pipe(takeUntil(this.destroy$))
      .subscribe({ next: (res: any) => { this.categorie = (res.data as ICategoria[]) ?? []; } });

    // Reagisce ai cambi di ID nell'URL (es. navigazione da una serie all'altra)
    this.route.paramMap
      .pipe(takeUntil(this.destroy$))
      .subscribe(params => {
        const id = Number(params.get('id'));
        if (id) this.caricaDettaglio(id);
      });
  }

  ngOnDestroy(): void {
    // Completa il Subject per cancellare tutte le sottoscrizioni attive
    this.destroy$.next();
    this.destroy$.complete();
  }

  // Carica i dati della serie TV e organizza gli episodi per stagione
  private caricaDettaglio(id: number): void {
    this.caricamento = true;
    this.videoAperto = false;

    this.apiService.getSingolaSerieTV(id)
      .pipe(takeUntil(this.destroy$))
      .subscribe({
        next: (r: any) => {
          this.serie = r.data ?? r;

          // Raggruppa gli episodi per numero di stagione e ordina le chiavi
          if (this.serie?.episodi) {
            this.stagioni = new Map();
            this.serie.episodi.forEach((ep: IEpisodio) => {
              const n = ep.numeroStagione ?? 1;
              if (!this.stagioni.has(n)) this.stagioni.set(n, []);
              this.stagioni.get(n)!.push(ep);
            });
            this.stagionNumbers = Array.from(this.stagioni.keys()).sort((a, b) => a - b);
            // Apre automaticamente la prima stagione disponibile
            if (this.stagionNumbers.length > 0) this.stagioneAperta = this.stagionNumbers[0];
          }

          this.caricamento = false;
        },
        error: (err: Error) => {
          this.errore = err.message;
          this.caricamento = false;
        }
      });
  }

  // Mostra o nasconde il player video principale della serie
  toggleVideo(): void {
    this.videoAperto = !this.videoAperto;
  }

  // Apre o chiude il pannello accordion della stagione selezionata
  apriStagione(n: number): void {
    this.stagioneAperta = this.stagioneAperta === n ? null : n;
  }

  // Restituisce la lista degli episodi per un dato numero di stagione
  episodiStagione(n: number): IEpisodio[] {
    return this.stagioni.get(n) ?? [];
  }

  // Mostra o nasconde il player video di un singolo episodio
  toggleVideoEpisodio(idEpisodio: number): void {
    this.videoApertoEpisodio = this.videoApertoEpisodio === idEpisodio ? null : idEpisodio;
  }

  // ===== MODIFICA SERIE =====

  // Precarica i campi del form con i dati attuali della serie, poi apre il modal
  apriModifica(content: TemplateRef<any>): void {
    if (!this.serie) return;
    this.formNome           = this.serie.nome ?? '';
    this.formIdCategoria    = this.serie.categoria?.id ?? 0;
    this.formDescrizione    = this.serie.descrizione ?? '';
    this.formTotaleStagioni = this.serie.totaleStagioni ?? null;
    this.formRegista        = this.serie.regista ?? '';
    this.formAttori         = this.serie.attori ?? '';
    this.formAnnoInizio     = this.serie.annoInizio ?? null;
    this.formAnnoFine       = this.serie.annoFine ?? null;
    this.formErrore         = '';
    this.formMessaggio      = '';
    this.formCaricamento    = false;
    this.formLocandina      = null; // null = nessun nuovo file → mantieni quello esistente
    this.formCarousel       = null;
    this.formVideo          = null;

    this.modalModificaRef = this.modalService.open(content, {
      size: 'lg', centered: true, backdrop: 'static', keyboard: false, scrollable: true
    });
    this.modalModificaRef.result.then(() => {}, () => {});
  }

  // Gestisce la selezione di un file (locandina / carousel / video) dall'input file
  onFileChange(event: Event, campo: 'locandina' | 'carousel' | 'video'): void {
    const input = event.target as HTMLInputElement;
    const file  = input.files?.[0] ?? null;
    if (campo === 'locandina') this.formLocandina = file;
    if (campo === 'carousel')  this.formCarousel  = file;
    if (campo === 'video')     this.formVideo     = file;
  }

  // Costruisce il FormData e invia la richiesta di modifica al backend
  salvaModifica(): void {
    if (!this.serie?.idSerie) return;
    if (!this.formNome.trim()) { this.formErrore = 'Il nome è obbligatorio.'; return; }

    this.formCaricamento = true;
    this.formErrore      = '';
    this.formMessaggio   = '';

    const fd = new FormData();
    fd.append('nome', this.formNome.trim());
    if (this.formIdCategoria)    fd.append('idCategoria',    String(this.formIdCategoria));
    if (this.formDescrizione)    fd.append('descrizione',    this.formDescrizione);
    if (this.formTotaleStagioni) fd.append('totaleStagioni', String(this.formTotaleStagioni));
    if (this.formRegista)        fd.append('regista',        this.formRegista);
    if (this.formAttori)         fd.append('attori',         this.formAttori);
    if (this.formAnnoInizio)     fd.append('annoInizio',     String(this.formAnnoInizio));
    if (this.formAnnoFine)       fd.append('annoFine',       String(this.formAnnoFine));
    // Aggiunge i file solo se l'utente ne ha selezionati di nuovi
    if (this.formLocandina) fd.append('locandina', this.formLocandina, this.formLocandina.name);
    if (this.formCarousel)  fd.append('carousel',  this.formCarousel,  this.formCarousel.name);
    if (this.formVideo)     fd.append('video',     this.formVideo,     this.formVideo.name);

    // Method spoofing: PHP non gestisce $_FILES con richieste PUT native.
    // Inviamo POST e aggiungiamo _method=PUT affinché Laravel instradi la
    // richiesta al metodo update() del controller (identico al pattern dei film).
    fd.append('_method', 'PUT');

    this.apiService.modificaSerieTVConFile(this.serie.idSerie, fd)
      .pipe(takeUntil(this.destroy$))
      .subscribe({
        next: () => {
          this.formCaricamento = false;
          this.formMessaggio   = 'Serie modificata con successo!';
          // Chiude il modal e ricarica i dati aggiornati dopo un breve ritardo visivo
          setTimeout(() => {
            this.modalModificaRef?.close();
            this.caricaDettaglio(this.serie!.idSerie);
          }, 900);
        },
        error: (err: Error) => {
          this.formCaricamento = false;
          this.formErrore = err?.message ?? 'Errore nel salvataggio.';
        }
      });
  }

  // ===== ELIMINA SERIE =====

  // Apre il modal di conferma eliminazione
  apriElimina(content: TemplateRef<any>): void {
    if (!this.serie) return;
    this.modalEliminaRef = this.modalService.open(content, { size: 'sm', centered: true });
    this.modalEliminaRef.result.then(() => {}, () => {});
  }

  // Esegue il soft-delete della serie tramite API DELETE e reindirizza al catalogo
  confermaElimina(): void {
    if (!this.serie?.idSerie) return;
    this.apiService.eliminaSerieTV(this.serie.idSerie)
      .pipe(takeUntil(this.destroy$))
      .subscribe({
        next: () => {
          this.modalEliminaRef?.close();
          this.router.navigate(['/catalogo']);
        },
        error: (err: Error) => {
          this.modalEliminaRef?.close();
          alert(err?.message ?? "Errore durante l'eliminazione.");
        }
      });
  }
}
