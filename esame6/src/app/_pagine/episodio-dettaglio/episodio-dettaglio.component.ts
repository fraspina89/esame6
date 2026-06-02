import { Component, OnInit, OnDestroy, TemplateRef } from '@angular/core';
import { ActivatedRoute, Router } from '@angular/router';
import { Subject } from 'rxjs';
import { takeUntil } from 'rxjs/operators';
import { NgbModal, NgbModalRef } from '@ng-bootstrap/ng-bootstrap';
import { ApiService } from '../../_servizi/api.service';
import { AuthService } from '../../_servizi/auth.service';
import { IEpisodio } from '../../_models';
import { Auth } from '../../_type/auth.type';
import { environment } from '../../../environments/environment';

@Component({
  selector: 'app-episodio-dettaglio',
  templateUrl: './episodio-dettaglio.component.html',
  styleUrls: ['./episodio-dettaglio.component.scss']
})
export class EpisodioDettaglioComponent implements OnInit, OnDestroy {

  readonly imgFallback = environment.locandineBase + '/elenco.jpg';
  episodio: IEpisodio | null = null;
  serieId: number = 0;
  serieNome: string = '';
  caricamento = true;
  errore = '';
  auth: Auth = AuthService.auth;
  videoAperto = false;

  // form modifica
  formTitolo = '';
  formDescrizione = '';
  formNumeroStagione: number | null = null;
  formNumeroEpisodio: number | null = null;
  formDurata: number | null = null;
  formAnno: number | null = null;
  formCaricamento = false;
  formErrore = '';
  formMessaggio = '';

  // form aggiungi
  aggTitolo = '';
  aggDescrizione = '';
  aggNumeroStagione: number | null = null;
  aggNumeroEpisodio: number | null = null;
  aggDurata: number | null = null;
  aggAnno: number | null = null;
  aggCaricamento = false;
  aggErrore = '';
  aggMessaggio = '';

  private modalModificaRef: NgbModalRef | null = null;
  private modalEliminaRef: NgbModalRef | null = null;
  private modalAggiungiRef: NgbModalRef | null = null;

  private destroy$ = new Subject<void>();

  constructor(
    private api: ApiService,
    private route: ActivatedRoute,
    private router: Router,
    private authService: AuthService,
    private modalService: NgbModal
  ) {}

  ngOnInit(): void {
    this.authService.leggiObsAuth()
      .pipe(takeUntil(this.destroy$))
      .subscribe((a: Auth) => this.auth = a);

    this.route.paramMap
      .pipe(takeUntil(this.destroy$))
      .subscribe(params => {
        const serieId = Number(params.get('serieId'));
        const id = Number(params.get('id'));
        if (serieId && id) {
          this.serieId = serieId;
          this.caricaEpisodio(serieId, id);
        }
      });
  }

  ngOnDestroy(): void {
    this.destroy$.next();
    this.destroy$.complete();
  }

  caricaEpisodio(serieId: number, idEpisodio: number): void {
    this.caricamento = true;
    this.episodio = null;
    this.videoAperto = false;
    this.api.getEpisodioData(serieId, idEpisodio)
      .pipe(takeUntil(this.destroy$))
      .subscribe({
        next: (ep) => {
          this.episodio = ep;
          this.serieNome = (ep as any)?.serie_tv?.nome ?? '';
          this.caricamento = false;
        },
        error: () => {
          this.errore = 'Episodio non trovato.';
          this.caricamento = false;
        }
      });
  }

  tornaSerie(): void {
    this.router.navigate(['/serie-tv', this.serieId]);
  }

  toggleVideo(): void {
    this.videoAperto = !this.videoAperto;
  }

  // ===== MODIFICA =====

  apriModifica(content: TemplateRef<any>): void {
    if (!this.episodio) return;
    this.formTitolo = this.episodio.titolo ?? '';
    this.formDescrizione = this.episodio.descrizione ?? '';
    this.formNumeroStagione = this.episodio.numeroStagione ?? null;
    this.formNumeroEpisodio = this.episodio.numeroEpisodio ?? null;
    this.formDurata = this.episodio.durata ?? null;
    this.formAnno = this.episodio.anno ?? null;
    this.formErrore = '';
    this.formMessaggio = '';
    this.formCaricamento = false;
    this.modalModificaRef = this.modalService.open(content, {
      size: 'lg', centered: true, backdrop: 'static', keyboard: false, scrollable: true
    });
    this.modalModificaRef.result.then(() => {}, () => {});
  }

  salvaModifica(): void {
    if (!this.episodio?.idEpisodio) return;
    if (!this.formTitolo.trim()) { this.formErrore = 'Il titolo è obbligatorio.'; return; }
    this.formCaricamento = true;
    this.formErrore = '';
    this.formMessaggio = '';
    const payload: any = {
      titolo: this.formTitolo.trim(),
      descrizione: this.formDescrizione || null,
      numeroStagione: this.formNumeroStagione,
      numeroEpisodio: this.formNumeroEpisodio,
      durata: this.formDurata,
      anno: this.formAnno
    };
    this.api.modificaEpisodio(this.serieId, this.episodio.idEpisodio, payload)
      .pipe(takeUntil(this.destroy$))
      .subscribe({
        next: () => {
          this.formCaricamento = false;
          this.formMessaggio = 'Episodio modificato con successo!';
          setTimeout(() => {
            this.modalModificaRef?.close();
            this.caricaEpisodio(this.serieId, this.episodio!.idEpisodio);
          }, 900);
        },
        error: (err: Error) => {
          this.formCaricamento = false;
          this.formErrore = err?.message ?? 'Errore nel salvataggio.';
        }
      });
  }

  // ===== ELIMINA =====

  apriElimina(content: TemplateRef<any>): void {
    if (!this.episodio) return;
    this.modalEliminaRef = this.modalService.open(content, { size: 'sm', centered: true });
    this.modalEliminaRef.result.then(() => {}, () => {});
  }

  confermaElimina(): void {
    if (!this.episodio?.idEpisodio) return;
    this.api.eliminaEpisodio(this.serieId, this.episodio.idEpisodio)
      .pipe(takeUntil(this.destroy$))
      .subscribe({
        next: () => {
          this.modalEliminaRef?.close();
          this.router.navigate(['/serie-tv', this.serieId]);
        },
        error: (err: Error) => {
          this.modalEliminaRef?.close();
          alert(err?.message ?? "Errore durante l'eliminazione.");
        }
      });
  }

  // ===== AGGIUNGI =====

  apriAggiungi(content: TemplateRef<any>): void {
    this.aggTitolo = '';
    this.aggDescrizione = '';
    this.aggNumeroStagione = this.episodio?.numeroStagione ?? null;
    this.aggNumeroEpisodio = null;
    this.aggDurata = null;
    this.aggAnno = null;
    this.aggErrore = '';
    this.aggMessaggio = '';
    this.aggCaricamento = false;
    this.modalAggiungiRef = this.modalService.open(content, {
      size: 'lg', centered: true, backdrop: 'static', keyboard: false, scrollable: true
    });
    this.modalAggiungiRef.result.then(() => {}, () => {});
  }

  salvaAggiungi(): void {
    if (!this.aggTitolo.trim()) { this.aggErrore = 'Il titolo è obbligatorio.'; return; }
    this.aggCaricamento = true;
    this.aggErrore = '';
    this.aggMessaggio = '';
    const fd = new FormData();
    fd.append('titolo', this.aggTitolo.trim());
    if (this.aggDescrizione) fd.append('descrizione', this.aggDescrizione);
    if (this.aggNumeroStagione !== null) fd.append('numeroStagione', String(this.aggNumeroStagione));
    if (this.aggNumeroEpisodio !== null) fd.append('numeroEpisodio', String(this.aggNumeroEpisodio));
    if (this.aggDurata !== null) fd.append('durata', String(this.aggDurata));
    if (this.aggAnno !== null) fd.append('anno', String(this.aggAnno));
    this.api.aggiungiEpisodio(this.serieId, fd)
      .pipe(takeUntil(this.destroy$))
      .subscribe({
        next: () => {
          this.aggCaricamento = false;
          this.aggMessaggio = 'Episodio aggiunto con successo!';
          setTimeout(() => { this.modalAggiungiRef?.close(); }, 900);
        },
        error: (err: Error) => {
          this.aggCaricamento = false;
          this.aggErrore = err?.message ?? 'Errore nel salvataggio.';
        }
      });
  }
}
