/*
  CatalogoCategoriaComponent
  - Pagina che mostra tutti i film di una specifica categoria.
  - Legge l'idCategoria dal parametro di rotta (:idCategoria).
  - Chiama l'API per ottenere i film filtrati per categoria
  - Usa destroy$ + takeUntil per evitare memory leak.
*/
import { Component, OnInit, OnDestroy } from '@angular/core';
import { ActivatedRoute, Router } from '@angular/router';
import { Subject } from 'rxjs';
import { takeUntil } from 'rxjs/operators';
import { ApiService } from '../../_servizi/api.service';
import { IFilm, ICategoria, ISerie } from '../../_models';
import { environment } from '../../../environments/environment';

@Component({
  selector: 'app-catalogo-categoria',
  templateUrl: './catalogo-categoria.component.html',
  styleUrls: ['./catalogo-categoria.component.scss']
})
export class CatalogoCategoriaComponent implements OnInit, OnDestroy {

  readonly imgFallback = environment.locandineBase + '/elenco.jpg';

  // ===== Dati pagina =====
  film: IFilm[] = [];
  serie: ISerie[] = [];
  categoria: ICategoria | null = null;
  idCategoria: number = 0;

  // ===== Stato UI =====
  caricamento = true;
  errore = '';
  hoveredFilmId: number | null = null;
  hoveredSerieId: number | null = null;

  // ===== Slide film =====
  primoIndice = 0;
  readonly FILM_PER_SLIDE = 5;

  // ===== Slide serie TV =====
  primoIndiceSerie = 0;

  // ===== RxJS cleanup =====
  private destroy$ = new Subject<void>();

  constructor(
    private route: ActivatedRoute,
    private router: Router,
    private api: ApiService
  ) {}

  ngOnInit(): void {
    // Legge il parametro :idCategoria dalla rotta e ricarica se cambia (es. cambio categoria dal menu)
    this.route.params.pipe(takeUntil(this.destroy$)).subscribe(params => {
      this.idCategoria = parseInt(params['idCategoria'], 10);
      this.primoIndice = 0;
      this.primoIndiceSerie = 0;
      this.caricaDati();
    });
  }

  ngOnDestroy(): void {
    this.destroy$.next();
    this.destroy$.complete();
  }

  // ===== Caricamento dati =====

  caricaDati(): void {
    this.caricamento = true;
    this.errore = '';
    this.film = [];
    this.serie = [];
    this.categoria = null;

    // Carica nome categoria
    this.api.getCategorie().pipe(takeUntil(this.destroy$)).subscribe({
      next: (res) => {
        const lista: ICategoria[] = Array.isArray(res.data) ? res.data : [];
        this.categoria = lista.find(c => c.idCategoria === this.idCategoria) ?? null;
      },
      error: () => { /* non blocca la pagina, titolo sarà vuoto */ }
    });

    // Carica film della categoria
    this.api.getFilmsPerCategoria(this.idCategoria).pipe(takeUntil(this.destroy$)).subscribe({
      next: (films) => {
        this.film = films;
        // Carica serie TV dopo i film
        this.api.richiestaProtetta(['serieTV'], 'GET').pipe(takeUntil(this.destroy$)).subscribe({
          next: (r) => {
            const tutte: ISerie[] = (r.data as ISerie[]) ?? [];
            this.serie = tutte.filter(s => s.categoria?.id === this.idCategoria);
            this.caricamento = false;
          },
          error: () => { this.caricamento = false; }
        });
      },
      error: (err) => {
        this.errore = err?.message ?? 'Errore nel caricamento dei film.';
        this.caricamento = false;
      }
    });
  }

  // ===== Navigazione =====

  vaiAlFilm(idFilm: number): void {
    this.router.navigate(['/catalogo', idFilm]);
  }

  tornaAlCatalogo(): void {
    this.router.navigate(['/catalogo']);
  }

  // ===== Slide =====

  filmVisibili(): IFilm[] {
    return this.film.slice(this.primoIndice, this.primoIndice + this.FILM_PER_SLIDE);
  }

  hasPrev(): boolean { return this.primoIndice > 0; }
  hasNext(): boolean { return this.primoIndice + this.FILM_PER_SLIDE < this.film.length; }

  slidePrev(): void {
    if (this.hasPrev()) this.primoIndice -= this.FILM_PER_SLIDE;
  }

  slideNext(): void {
    if (this.hasNext()) this.primoIndice += this.FILM_PER_SLIDE;
  }

  // ===== Slide Serie TV =====

  serieVisibili(): ISerie[] {
    return this.serie.slice(this.primoIndiceSerie, this.primoIndiceSerie + this.FILM_PER_SLIDE);
  }

  hasPrevSerie(): boolean { return this.primoIndiceSerie > 0; }
  hasNextSerie(): boolean { return this.primoIndiceSerie + this.FILM_PER_SLIDE < this.serie.length; }

  slidePrevSerie(): void {
    if (this.hasPrevSerie()) this.primoIndiceSerie -= this.FILM_PER_SLIDE;
  }

  slideNextSerie(): void {
    if (this.hasNextSerie()) this.primoIndiceSerie += this.FILM_PER_SLIDE;
  }

  vaiAllaSerieTV(id?: number | null): void {
    if (!id) return;
    this.router.navigate(['/serie-tv', id]);
  }

  // ===== Hover video =====

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

  onHoverLeaveSerie(s: ISerie, event: Event): void {
    this.hoveredSerieId = null;
    const card = event.currentTarget as HTMLElement;
    const video = card.querySelector<HTMLVideoElement>('video.card-video');
    if (video) { video.pause(); }
    card.classList.remove('playing', 'video-ready');
  }
}
