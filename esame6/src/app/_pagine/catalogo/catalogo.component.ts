/*
  CatalogoComponent
  - Pagina principale del catalogo (accessibile solo agli utenti autenticati).
  - Funzionalità:
    * Carica tutte le categorie, i film e le serie TV dal backend tramite ApiService.
    * Raggruppa film e serie per categoria in caroselli orizzontali scorrevoli .
    * Gestisce la ricerca film per titolo tramite queryParam `?search=...`.
    * Permette lo scroll automatico a una categoria tramite queryParam `?categoria=...`.
    * Al passaggio del mouse su una card film, avvia la riproduzione del video (muted).
    * Solo agli Amministratori (idRuolo === 1) mostrano i bottoni "Aggiungi Film" e "Aggiungi Serie TV".
    * Tramite modal NgBootstrap permette all'Admin di aggiungere un nuovo film o una nuova serie TV
      con upload dei file (locandina, carousel, video).
  - Usa `Subject destroy$` + `takeUntil` per cancellare tutte le sottoscrizioni RxJS
    quando il componente viene distrutto (evita memory leak).
*/
import { Component, HostListener, OnInit, OnDestroy, TemplateRef } from '@angular/core';
import { ActivatedRoute, Router } from '@angular/router';
import { Subject } from 'rxjs';
import { takeUntil } from 'rxjs/operators';
import { NgbModal, NgbModalRef } from '@ng-bootstrap/ng-bootstrap';
import { ApiService } from '../../_servizi/api.service';
import { AuthService } from '../../_servizi/auth.service';
import { IFilm, ICategoria, ISerie } from '../../_models';
import { Auth } from '../../_type/auth.type';
import { environment } from '../../../environments/environment';

// Struttura locale che accoppia una categoria con i suoi film e serie TV
interface CategoriaConFilm {
  categoria: ICategoria;
  film: IFilm[];
  primoIndice: number;  // indice del primo film visibile nella slide )
  serie: ISerie[];
  primoIndiceSerie: number;
}

@Component({
  selector: 'app-catalogo',
  templateUrl: './catalogo.component.html',
  styleUrls: ['./catalogo.component.scss']
})
export class CatalogoComponent implements OnInit, OnDestroy {

  readonly imgFallback = environment.locandineBase + '/elenco.jpg';
  readonly carouselBase = environment.carouselBase;

  // ===== Stato generale =====
  categorieConFilm: CategoriaConFilm[] = [];
  caricamento = true;   // true mentre si attendono i dati dal server
  errore = '';          // messaggio di errore se la chiamata API fallisce

  // ===== Ricerca =====
  queryRicerca = '';           // testo digitato nella barra di ricerca
  risultatiRicerca: IFilm[] = []; // film trovati dalla ricerca
  ricercaAttiva = false;       // true se la ricerca è attiva (mostra i risultati invece del catalogo)

  // ===== Modal film selezionato =====
  filmSelezionato: IFilm | null = null; // film scelto per la visualizzazione nel modal
  private modalRef: NgbModalRef | null = null;
  hoveredFilmId: number | null = null;  // ID del film con il mouse sopra (per avviare il video)
  hoveredSerieId: number | null = null; // ID della serie con il mouse sopra (per mostrare descrizione)

  // ===== Autenticazione =====
  auth: Auth = AuthService.auth; // stato corrente dell'utente (aggiornato tramite subscribe)

  // ===== Categorie per il dropdown del form "Aggiungi Film" =====
  categorie: ICategoria[] = [];

  // ===== Campi del form "Aggiungi Film" =====
  formTitolo      = '';
  formIdCategoria = 0;           // 0 = nessuna categoria selezionata
  formDescrizione = '';
  formAnno: number | null = null;
  formDurata: number | null = null;
  formRegista     = '';
  formAttori      = '';
  formLocandina: File | null = null; // file immagine locandina selezionato dall'utente
  formCarousel: File | null = null;  // file immagine carousel
  formVideo: File | null = null;     // file video
  formCaricamento = false;  // true durante l'invio della richiesta al server
  formErrore      = '';     // messaggio di errore del form
  formMessaggio   = '';     // messaggio di successo del form
  private modalAggiungiRef: NgbModalRef | null = null;

  // ===== Campi del form "Aggiungi Serie TV" =====
  serieFormNome           = '';
  serieFormIdCategoria    = 0;           // 0 = nessuna categoria selezionata
  serieFormDescrizione    = '';
  serieFormTotaleStagioni: number | null = null;
  serieFormAnnoInizio: number | null = null;
  serieFormAnnoFine: number | null = null;
  serieFormRegista        = '';
  serieFormAttori         = '';
  serieFormCaricamento    = false;       // true durante l'invio al server
  serieFormErrore         = '';          // messaggio di errore del form
  serieFormMessaggio      = '';          // messaggio di successo del form
  serieFormLocandina: File | null = null; // file locandina selezionato
  serieFormCarousel: File | null  = null; // file carousel selezionato
  serieFormVideo: File | null     = null; // file video selezionato
  private modalAggiungiSerieRef: NgbModalRef | null = null;

  // Numero di film da mostrare per ogni slide orizzontale (5 su desktop ≥992px, 4 su tablet)
  get FILM_PER_SLIDE(): number { return window.innerWidth >= 992 ? 5 : 4; }

  // Forza il ricalcolo di FILM_PER_SLIDE e re-render delle slide quando la viewport cambia
  @HostListener('window:resize')
  onResize(): void {}

  private destroy$ = new Subject<void>();

  constructor(
    private api: ApiService,
    private route: ActivatedRoute,
    private router: Router,
    private modalService: NgbModal,
    private authService: AuthService
  ) {}

  ngOnInit(): void {
    this.authService.leggiObsAuth()
      .pipe(takeUntil(this.destroy$))
      .subscribe((a: Auth) => this.auth = a);

    this.caricaCatalogo();

    // Gestisce i queryParams sia al primo caricamento sia quando cambiano
    this.route.queryParamMap
      .pipe(takeUntil(this.destroy$))
      .subscribe(params => {
        const q = params.get('search');
        if (q) { this.queryRicerca = q; this.cercaFilm(); }

        const cat = params.get('categoria');
        if (cat) {
          // Se il catalogo è già caricato scrollo subito, altrimenti aspetto
          const tryScroll = () => {
            const el = document.getElementById('categoria-' + cat);
            if (el) {
              el.scrollIntoView({ behavior: 'smooth', block: 'start' });
            }
          };
          if (!this.caricamento) {
            setTimeout(tryScroll, 100);
          } else {
            // Aspetta che caricamento diventi false controllando ad intervalli
            const interval = setInterval(() => {
              if (!this.caricamento) {
                clearInterval(interval);
                setTimeout(tryScroll, 100);
              }
            }, 100);
          }
        }
      });
  }

  ngOnDestroy(): void {
    this.destroy$.next();
    this.destroy$.complete();
  }

  caricaCatalogo(): void {
    this.caricamento = true;
    this.categorieConFilm = [];
    const categoriaTarget = this.route.snapshot.queryParamMap.get('categoria');
    this.api.getCategorie()
      .pipe(takeUntil(this.destroy$))
      .subscribe({
        next: (res) => {
          const categorie: ICategoria[] = (res.data as ICategoria[]) ?? [];
          this.categorie = categorie;
          // Inizializza tutte le categorie subito con array vuoti
          categorie.forEach(cat => {
            this.categorieConFilm.push({ categoria: cat, film: [], primoIndice: 0, serie: [], primoIndiceSerie: 0 });
          });
          this.categorieConFilm.sort((a, b) => a.categoria.idCategoria - b.categoria.idCategoria);

          if (categorie.length === 0) {
            this.caricamento = false;
            return;
          }
          let completateFilm = 0;
          categorie.forEach(cat => {
            this.api.getFilmsPerCategoria(cat.idCategoria)
              .pipe(takeUntil(this.destroy$))
              .subscribe({
                next: (film) => {
                  const cc = this.categorieConFilm.find(c => c.categoria.idCategoria === cat.idCategoria);
                  if (cc) cc.film = film;
                  completateFilm++;
                  if (completateFilm === categorie.length) {
                    this.caricaSerieTV(categoriaTarget);
                  }
                },
                error: () => {
                  completateFilm++;
                  if (completateFilm === categorie.length) this.caricaSerieTV(categoriaTarget);
                }
              });
          });
        },
        error: () => {
          this.errore = 'Impossibile caricare il catalogo.';
          this.caricamento = false;
        }
      });
  }

  // Carica tutte le serie TV, le raggruppa per categoria e aggiorna categorieConFilm
  private caricaSerieTV(categoriaTarget: string | null): void {
    this.api.getSerieTVItems()
      .pipe(takeUntil(this.destroy$))
      .subscribe({
        next: (serie: ISerie[]) => {
          // Costruisce una mappa idCategoria → lista serie
          const mappa = new Map<number, ISerie[]>();
          serie.forEach(s => {
            if (s.categoria) {
              const idCat = s.categoria.id;
              if (!mappa.has(idCat)) mappa.set(idCat, []);
              mappa.get(idCat)!.push(s);
            }
          });
          // Ricostruisce l'array con nuovi riferimenti per forzare il re-render di Angular
          this.categorieConFilm = this.categorieConFilm.map(cc => ({
            ...cc,
            serie: mappa.get(cc.categoria.idCategoria) ?? []
          }));
          this.caricamento = false;
          // Scroll alla categoria target se indicata nel queryParam
          if (categoriaTarget) {
            setTimeout(() => {
              const el = document.getElementById('categoria-' + categoriaTarget);
              if (el) el.scrollIntoView({ behavior: 'smooth', block: 'start' });
            }, 100);
          }
        },
        error: () => {
          this.caricamento = false;
        }
      });
  }

  // Slide precedente
  slidePrev(cc: CategoriaConFilm): void {
    if (cc.primoIndice > 0) cc.primoIndice -= this.FILM_PER_SLIDE;
  }

  // Slide successiva
  slideNext(cc: CategoriaConFilm): void {
    if (cc.primoIndice + this.FILM_PER_SLIDE < cc.film.length) {
      cc.primoIndice += this.FILM_PER_SLIDE;
    }
  }

  filmVisibili(cc: CategoriaConFilm): IFilm[] {
    return cc.film.slice(cc.primoIndice, cc.primoIndice + this.FILM_PER_SLIDE);
  }

  hasPrev(cc: CategoriaConFilm): boolean { return cc.primoIndice > 0; }
  hasNext(cc: CategoriaConFilm): boolean { return cc.primoIndice + this.FILM_PER_SLIDE < cc.film.length; }

  // ===== Slide Serie TV =====

  // Restituisce le serie TV visibili nella slide corrente 
  serieVisibili(cc: CategoriaConFilm): ISerie[] {
    return cc.serie.slice(cc.primoIndiceSerie, cc.primoIndiceSerie + this.FILM_PER_SLIDE);
  }

  // Verifica se esiste una slide precedente per le serie TV
  hasPrevSerie(cc: CategoriaConFilm): boolean { return cc.primoIndiceSerie > 0; }

  // Verifica se esiste una slide successiva per le serie TV
  hasNextSerie(cc: CategoriaConFilm): boolean { return cc.primoIndiceSerie + this.FILM_PER_SLIDE < cc.serie.length; }

  // Scorre alla slide precedente delle serie TV
  slidePrevSerie(cc: CategoriaConFilm): void {
    if (cc.primoIndiceSerie > 0) cc.primoIndiceSerie -= this.FILM_PER_SLIDE;
  }

  // Scorre alla slide successiva delle serie TV
  slideNextSerie(cc: CategoriaConFilm): void {
    if (cc.primoIndiceSerie + this.FILM_PER_SLIDE < cc.serie.length) {
      cc.primoIndiceSerie += this.FILM_PER_SLIDE;
    }
  }

  // Naviga alla pagina di dettaglio della serie TV selezionata
  vaiAllaSerieTV(id?: number | null): void {
    if (!id) return;
    this.router.navigate(['/serie-tv', id]);
  }

  apriModal(film: IFilm, content?: TemplateRef<any>): void {
    this.filmSelezionato = film;
    if (content) {
      this.modalRef = this.modalService.open(content, {
        size: 'lg',
        centered: true,
        backdrop: true,
        keyboard: true
      });
      this.modalRef.result.then(() => { this.filmSelezionato = null; }, () => { this.filmSelezionato = null; });
    }
  }

  chiudiModal(): void {
    if (this.modalRef) { this.modalRef.close(); this.modalRef = null; }
    this.filmSelezionato = null;
  }

  cercaFilm(): void {
    const q = this.queryRicerca.trim();
    if (!q) { this.ricercaAttiva = false; this.risultatiRicerca = []; return; }
    this.ricercaAttiva = true;
    this.api.searchFilms(q)
      .pipe(takeUntil(this.destroy$))
      .subscribe({
        next: (film) => { this.risultatiRicerca = film; },
        error: () => { this.risultatiRicerca = []; }
      });
  }

  resetRicerca(): void {
    this.queryRicerca = '';
    this.ricercaAttiva = false;
    this.risultatiRicerca = [];
    this.router.navigate([], { queryParams: {}, replaceUrl: true });
  }

  onHoverEnter(film: IFilm, event: Event): void {
    this.hoveredFilmId = film.idFilm ?? null;
    const card = event.currentTarget as HTMLElement;
    const video = card.querySelector<HTMLVideoElement>('video.card-video');
    card.classList.add('playing');
    if (!video) return;
    if (!video.getAttribute('src') && video.dataset['src']) {
      video.src = video.dataset['src'];
    }
    video.muted = true;
    video.play()
      .then(() => {
        if (this.hoveredFilmId === film.idFilm) card.classList.add('video-ready');
      })
      .catch(() => {});
  }

  onHoverLeave(film: IFilm, event: Event): void {
    this.hoveredFilmId = null;
    const card = event.currentTarget as HTMLElement;
    const video = card.querySelector<HTMLVideoElement>('video.card-video');
    if (video) { video.pause(); }
    card.classList.remove('playing', 'video-ready');
  }

  // Attiva l'effetto hover sulla card serie TV 
  onHoverEnterSerie(s: ISerie, event: Event): void {
    this.hoveredSerieId = s.idSerie ?? null;
    const card = event.currentTarget as HTMLElement;
    const video = card.querySelector<HTMLVideoElement>('video.card-video');
    card.classList.add('playing');
    if (!video) return;
    if (!video.getAttribute('src') && video.dataset['src']) {
      video.src = video.dataset['src'];
    }
    video.muted = true;
    video.play()
      .then(() => {
        if (this.hoveredSerieId === s.idSerie) card.classList.add('video-ready');
      })
      .catch(() => {});
  }

  // Rimuove l'effetto hover dalla card serie TV
  onHoverLeaveSerie(s: ISerie, event: Event): void {
    this.hoveredSerieId = null;
    const card = event.currentTarget as HTMLElement;
    const video = card.querySelector<HTMLVideoElement>('video.card-video');
    if (video) { video.pause(); }
    card.classList.remove('playing', 'video-ready');
  }

  // Naviga alla pagina dettaglio film
  vaiAlFilm(id?: number | null): void {
    if (!id) return;
    this.router.navigate(['/catalogo', id]);
  }

  // ===== AGGIUNGI SERIE TV (solo Admin) =====

  // Resetta il form e apre il modal per aggiungere una nuova serie TV
  apriModalAggiungiSerie(content: TemplateRef<any>, idCategoria: number = 0): void {
    this.resetFormSerie();
    this.serieFormIdCategoria = idCategoria;
    this.modalAggiungiSerieRef = this.modalService.open(content, {
      size: 'lg',
      centered: true,
      backdrop: 'static',
      keyboard: false,
      scrollable: true
    });
    this.modalAggiungiSerieRef.result.then(() => {}, () => {});
  }

  // Azzera tutti i campi del form "Aggiungi Serie TV"
  resetFormSerie(): void {
    this.serieFormNome           = '';
    this.serieFormIdCategoria    = 0;
    this.serieFormDescrizione    = '';
    this.serieFormTotaleStagioni = null;
    this.serieFormAnnoInizio     = null;
    this.serieFormAnnoFine       = null;
    this.serieFormRegista        = '';
    this.serieFormAttori         = '';
    this.serieFormCaricamento    = false;
    this.serieFormErrore         = '';
    this.serieFormMessaggio      = '';
    this.serieFormLocandina      = null;
    this.serieFormCarousel       = null;
    this.serieFormVideo          = null;
  }

  // Gestisce la selezione di un file (locandina / carousel / video) nel form aggiungi serie
  onSerieFileChange(event: Event, campo: 'locandina' | 'carousel' | 'video'): void {
    const input = event.target as HTMLInputElement;
    const file  = input.files?.[0] ?? null;
    if (campo === 'locandina') this.serieFormLocandina = file;
    if (campo === 'carousel')  this.serieFormCarousel  = file;
    if (campo === 'video')     this.serieFormVideo     = file;
  }

  // Valida il form, costruisce il FormData e invia la nuova serie TV al backend
  salvaSerie(): void {
    this.serieFormErrore    = '';
    this.serieFormMessaggio = '';

    // Validazione campi obbligatori
    if (!this.serieFormNome.trim())      { this.serieFormErrore = 'Il nome è obbligatorio.'; return; }
    if (this.serieFormIdCategoria === 0) { this.serieFormErrore = 'La categoria è obbligatoria.'; return; }

    this.serieFormCaricamento = true;

    const fd = new FormData();
    fd.append('nome',         this.serieFormNome.trim());
    fd.append('idCategoria',  String(this.serieFormIdCategoria));
    if (this.serieFormDescrizione.trim()) fd.append('descrizione', this.serieFormDescrizione);
    if (this.serieFormTotaleStagioni)     fd.append('totaleStagioni', String(this.serieFormTotaleStagioni));
    if (this.serieFormAnnoInizio)         fd.append('annoInizio', String(this.serieFormAnnoInizio));
    if (this.serieFormAnnoFine)           fd.append('annoFine', String(this.serieFormAnnoFine));
    if (this.serieFormRegista.trim())     fd.append('regista', this.serieFormRegista);
    if (this.serieFormAttori.trim())      fd.append('attori', this.serieFormAttori);
    // Aggiunge i file solo se selezionati dall'utente
    if (this.serieFormLocandina) fd.append('locandina', this.serieFormLocandina, this.serieFormLocandina.name);
    if (this.serieFormCarousel)  fd.append('carousel',  this.serieFormCarousel,  this.serieFormCarousel.name);
    if (this.serieFormVideo)     fd.append('video',     this.serieFormVideo,     this.serieFormVideo.name);

    this.api.aggiungiSerieTV(fd)
      .pipe(takeUntil(this.destroy$))
      .subscribe({
        next: () => {
          this.serieFormCaricamento = false;
          this.serieFormMessaggio   = 'Serie TV aggiunta con successo!';
          // Chiude il modal e ricarica il catalogo aggiornato dopo un breve ritardo visivo
          setTimeout(() => {
            this.modalAggiungiSerieRef?.close();
            this.caricaCatalogo();
          }, 900);
        },
        error: (err) => {
          this.serieFormCaricamento = false;
          const msg = err?.error?.message ?? err?.message ?? 'Errore nel salvataggio.';
          this.serieFormErrore = msg;
        }
      });
  }

  // ===== AGGIUNGI FILM (solo Admin) =====

  apriModalAggiungi(content: TemplateRef<any>, idCategoria: number = 0): void {
    this.resetFormFilm();
    this.formIdCategoria = idCategoria;
    this.modalAggiungiRef = this.modalService.open(content, {
      size: 'lg',
      centered: true,
      backdrop: 'static',
      keyboard: false,
      scrollable: true
    });
    this.modalAggiungiRef.result.then(() => {}, () => {});
  }

  resetFormFilm(): void {
    this.formTitolo      = '';
    this.formIdCategoria = 0;
    this.formDescrizione = '';
    this.formAnno        = null;
    this.formDurata      = null;
    this.formRegista     = '';
    this.formAttori      = '';
    this.formLocandina   = null;
    this.formCarousel    = null;
    this.formVideo       = null;
    this.formErrore      = '';
    this.formMessaggio   = '';
    this.formCaricamento = false;
  }

  onFileChange(event: Event, campo: 'locandina' | 'carousel' | 'video'): void {
    const input = event.target as HTMLInputElement;
    const file  = input.files?.[0] ?? null;
    if (campo === 'locandina') this.formLocandina = file;
    if (campo === 'carousel')  this.formCarousel  = file;
    if (campo === 'video')     this.formVideo     = file;
  }

  salvaFilm(): void {
    this.formErrore    = '';
    this.formMessaggio = '';

    if (!this.formTitolo.trim())          { this.formErrore = 'Il titolo è obbligatorio.'; return; }
    if (this.formIdCategoria === 0)        { this.formErrore = 'La categoria è obbligatoria.'; return; }
    if (!this.formDescrizione.trim())     { this.formErrore = 'La descrizione è obbligatoria.'; return; }
    if (!this.formAnno)                    { this.formErrore = "L'anno è obbligatorio."; return; }
    if (!this.formDurata)                  { this.formErrore = 'La durata è obbligatoria.'; return; }
    if (!this.formRegista.trim())         { this.formErrore = 'Il regista è obbligatorio.'; return; }
    if (!this.formAttori.trim())          { this.formErrore = 'Gli attori sono obbligatori.'; return; }

    this.formCaricamento = true;

    const fd = new FormData();
    fd.append('titolo',      this.formTitolo.trim());
    fd.append('idCategoria', String(this.formIdCategoria));
    fd.append('descrizione', this.formDescrizione);
    fd.append('anno',        String(this.formAnno));
    fd.append('durata',      String(this.formDurata));
    fd.append('regista',     this.formRegista);
    fd.append('attori',      this.formAttori);

    if (this.formLocandina) {
      fd.append('locandina', this.formLocandina, this.formLocandina.name);
    } else {
      fd.append('locandina_url', 'elenco.jpg');
    }
    if (this.formCarousel) {
      fd.append('carousel', this.formCarousel, this.formCarousel.name);
    }
    if (this.formVideo) {
      fd.append('video', this.formVideo, this.formVideo.name);
    }

    this.api.aggiungiFilm(fd)
      .pipe(takeUntil(this.destroy$))
      .subscribe({
        next: () => {
          this.formCaricamento = false;
          this.formMessaggio   = 'Film aggiunto con successo!';
          setTimeout(() => {
            this.modalAggiungiRef?.close();
            this.caricaCatalogo();
          }, 900);
        },
        error: (err) => {
          this.formCaricamento = false;
          const msg = err?.error?.message ?? err?.message ?? 'Errore nel salvataggio.';
          this.formErrore = msg;
        }
      });
  }
}
