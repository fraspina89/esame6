/*
  FilmDettaglioComponent
  - Pagina di dettaglio di un singolo film, raggiungibile tramite /catalogo/:id.
  - Funzionalità:
    * Carica il film dal backend in base all'ID presente nell'URL.
    * Mostra titolo, descrizione, anno, durata, regista, attori, locandina e video.
    * Carica una sezione "Ti potrebbe piacere" con 5 film casuali (escludendo il corrente).
    * Permette la riproduzione del video del film cliccando su "Guarda".
    * Solo agli Amministratori (idRuolo === 1) mostra i bottoni Modifica ed Elimina.
   
*/
import { Component, HostListener, OnInit, OnDestroy, TemplateRef } from '@angular/core';
import { ActivatedRoute, Router } from '@angular/router';
import { Subject } from 'rxjs';
import { takeUntil } from 'rxjs/operators';
import { NgbModal, NgbModalRef } from '@ng-bootstrap/ng-bootstrap';
import { ApiService } from '../../_servizi/api.service';
import { AuthService } from '../../_servizi/auth.service';
import { IFilm, ICategoria } from '../../_models';
import { Auth } from '../../_type/auth.type';
import { environment } from '../../../environments/environment';

@Component({
  selector: 'app-film-dettaglio',
  templateUrl: './film-dettaglio.component.html',
  styleUrls: ['./film-dettaglio.component.scss']
})
export class FilmDettaglioComponent implements OnInit, OnDestroy {

  readonly imgFallback = environment.locandineBase + '/elenco.jpg';

  // ===== Stato generale =====
  film: IFilm | null = null;      // dati del film corrente
  top5Settimana: IFilm[] = [];    // 5 film casuali per la sezione "Ti potrebbe piacere"
  caricamento = true;             // true mentre si caricano i dati
  errore = '';                    // messaggio di errore se il film non viene trovato
  videoAperto = false;            // true quando il player video è visibile
  auth: Auth = AuthService.auth;  // stato autenticazione (aggiornato tramite subscribe)
  hoveredFilmId: number | null = null; // ID film con mouse sopra (per anteprima video nella sezione correlati)

  // ===== Categorie per il dropdown del form modifica =====
  categorie: ICategoria[] = [];

  // ===== Campi del form "Modifica Film" =====
  formTitolo      = '';
  formIdCategoria = 0;
  formDescrizione = '';
  formAnno: number | null = null;
  formDurata: number | null = null;
  formRegista     = '';
  formAttori      = '';
  formLocandina: File | null = null; // nuovo file locandina (null = mantieni quello esistente)
  formCarousel: File | null = null;
  formVideo: File | null = null;
  formCaricamento = false;  // true durante l'invio
  formErrore      = '';     // messaggio errore form
  formMessaggio   = '';     // messaggio successo form

  private modalModificaRef: NgbModalRef | null = null;
  private modalEliminaRef: NgbModalRef | null = null;

  private destroy$ = new Subject<void>();

  constructor(
    private route: ActivatedRoute,
    private api: ApiService,
    private router: Router,
    private authService: AuthService,
    private modalService: NgbModal
  ) {}

  ngOnInit(): void {
    this.authService.leggiObsAuth()
      .pipe(takeUntil(this.destroy$))
      .subscribe((a: Auth) => this.auth = a);

    // Carica le categorie per il form di modifica
    this.api.getCategorie()
      .pipe(takeUntil(this.destroy$))
      .subscribe({
        next: (res) => { this.categorie = (res.data as ICategoria[]) ?? []; }
      });

    this.route.paramMap
      .pipe(takeUntil(this.destroy$))
      .subscribe(params => {
        const id = Number(params.get('id'));
        if (id) this.caricaFilm(id);
      });
  }

  ngOnDestroy(): void {
    this.destroy$.next();
    this.destroy$.complete();
  }

  caricaFilm(id: number): void {
    this.caricamento = true;
    this.film = null;
    this.top5Settimana = [];
    this.videoAperto = false;

    this.api.getFilmById(id)
      .pipe(takeUntil(this.destroy$))
      .subscribe({
        next: (film) => {
          this.film = film;
          this.caricamento = false;
          this.caricaTop5(id);
        },
        error: () => {
          this.errore = 'Film non trovato.';
          this.caricamento = false;
        }
      });
  }

  caricaTop5(idEscluso: number): void {
    this.api.getFilmsItems()
      .pipe(takeUntil(this.destroy$))
      .subscribe({
        next: (films) => {
          // Esclude il film corrente, mescola e prende i primi 5
          const altri = films.filter(f => f.idFilm !== idEscluso);
          this.top5Settimana = altri
            .sort(() => Math.random() - 0.5)
            .slice(0, 5);
        }
      });
  }

  vaiAlFilm(id: number): void {
    if (!id) return;
    this.videoAperto = false;
    this.hoveredFilmId = null;
    this.router.navigate(['/catalogo', id]);
  }

  // Forza il re-render del layout responsive al cambio dimensione viewport
  @HostListener('window:resize')
  onResize(): void {}

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

  toggleVideo(): void {
    this.videoAperto = !this.videoAperto;
  }

  modificaFilm(content: TemplateRef<any>): void {
    if (!this.film) return;
    // Precarica i campi del form con i dati attuali del film
    this.formTitolo      = this.film.titolo      ?? '';
    this.formIdCategoria = this.film.idCategoria ?? 0;
    this.formDescrizione = this.film.descrizione ?? '';
    this.formAnno        = this.film.anno        ?? null;
    this.formDurata      = this.film.durata      ?? null;
    this.formRegista     = this.film.regista     ?? '';
    this.formAttori      = this.film.attori      ?? '';
    this.formLocandina   = null;
    this.formCarousel    = null;
    this.formVideo       = null;
    this.formErrore      = '';
    this.formMessaggio   = '';
    this.formCaricamento = false;

    this.modalModificaRef = this.modalService.open(content, {
      size: 'lg',
      centered: true,
      backdrop: 'static',
      keyboard: false,
      scrollable: true
    });
    this.modalModificaRef.result.then(() => {}, () => {});
  }

  onFileChange(event: Event, campo: 'locandina' | 'carousel' | 'video'): void {
    const input = event.target as HTMLInputElement;
    const file  = input.files?.[0] ?? null;
    if (campo === 'locandina') this.formLocandina = file;
    if (campo === 'carousel')  this.formCarousel  = file;
    if (campo === 'video')     this.formVideo     = file;
  }

  salvaModifica(): void {
    if (!this.film?.idFilm) return;
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
    if (this.formLocandina)   fd.append('locandina',   this.formLocandina, this.formLocandina.name);
    if (this.formCarousel)    fd.append('carousel',    this.formCarousel,  this.formCarousel.name);
    if (this.formVideo)       fd.append('video',       this.formVideo,     this.formVideo.name);
    // Method spoofing: PHP non supporta $_FILES con PUT, quindi inviamo POST
    // ma aggiungiamo _method=PUT affinché Laravel lo instradi alla route PUT
    fd.append('_method', 'PUT');

    this.api.modificaFilm(this.film.idFilm, fd)
      .pipe(takeUntil(this.destroy$))
      .subscribe({
        next: () => {
          this.formCaricamento = false;
          this.formMessaggio   = 'Film modificato con successo!';
          setTimeout(() => {
            this.modalModificaRef?.close();
            this.caricaFilm(this.film!.idFilm);
          }, 900);
        },
        error: (err) => {
          this.formCaricamento = false;
          const msg = err?.error?.message ?? err?.message ?? 'Errore nel salvataggio.';
          this.formErrore = msg;
        }
      });
  }

  eliminaFilm(content: TemplateRef<any>): void {
    if (!this.film) return;
    this.modalEliminaRef = this.modalService.open(content, {
      size: 'sm',
      centered: true
    });
    this.modalEliminaRef.result.then(() => {}, () => {});
  }

  confermaElimina(): void {
    if (!this.film?.idFilm) return;
    this.api.eliminaFilm(this.film.idFilm)
      .pipe(takeUntil(this.destroy$))
      .subscribe({
        next: () => {
          this.modalEliminaRef?.close();
          this.router.navigate(['/catalogo']);
        },
        error: (err) => {
          this.modalEliminaRef?.close();
          const msg = err?.error?.message ?? 'Errore durante l\'eliminazione.';
          alert(msg);
        }
      });
  }
}
