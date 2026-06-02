import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { firstValueFrom, of } from 'rxjs';
import { tap, catchError } from 'rxjs/operators';
import { environment } from '../../environments/environment';

/**
 * Interfaccia tipizzata per un elemento di configurazione restituito dal backend.
 */
export interface ConfigItem {
  idConfigurazione?: number;
  chiave: string;
  valore: string;
}

@Injectable({ providedIn: 'root' })
export class ConfigService {

  private config: ConfigItem[] | null = null

  constructor(private http: HttpClient) { }

  /**
   * Carica la configurazione dal backend e la memorizza in memoria.
   * Ritorna una Promise risolta con i dati della configurazione.
   */
  loadConfig(): Promise<ConfigItem[]> {
    if (this.config !== null) return Promise.resolve(this.config)
    return firstValueFrom(
      this.http.get<ConfigItem[]>(`${environment.apiBase}/v1/configurazioni`).pipe(
        tap((c: ConfigItem[]) => this.config = c),
        catchError(err => {
          // Se il backend rifiuta (es. 403) non blocchiamo il bootstrap: usiamo fallback vuoto
          console.warn('ConfigService: impossibile caricare da API, fallback a configurazione vuota', err)
          this.config = []
          return of([] as ConfigItem[])
        })
      )
    )
  }

  /**
   * Restituisce il valore come stringa, oppure null se non esiste.
   */
  getString(key: string): string | null {
    if (!this.config) return null
    const item = this.config.find(x => x.chiave === key)
    return item ? item.valore : null
  }

  /**
   * Restituisce il valore parsato come numero, oppure null se non è un numero valido.
   */
  getNumber(key: string): number | null {
    const s = this.getString(key)
    if (s === null) return null
    const n = Number(s)
    return isNaN(n) ? null : n
  }

  /**
   * Restituisce il valore parsato come booleano ("1","true","yes" => true), oppure null.
   */
  getBoolean(key: string): boolean | null {
    const s = this.getString(key)
    if (s === null) return null
    const low = s.trim().toLowerCase()
    if (low === '1' || low === 'true' || low === 'yes') return true
    if (low === '0' || low === 'false' || low === 'no') return false
    return null
  }

  /**
   * Metodo generico che cerca di deserializzare il valore JSON e castarlo a T,
   * altrimenti ritorna il valore raw string o null.
   */
  get<T = any>(key: string): T | string | null {
    const s = this.getString(key)
    if (s === null) return null
    try {
      return JSON.parse(s) as T
    } catch (e) {
      return s
    }
  }
}
