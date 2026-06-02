/*
    ApiService
    - Servizio centrale per tutte le chiamate HTTP verso l'API backend.
    - Fornisce metodi helper per costruire l'URL (`calcolaRisorsa`) e per eseguire
        chiamate generiche (`richiestaGenerica`).
    - Implementa la procedura di login in 2 fasi (`getLoginFase1`, `getLoginFase2`, `login`).
    - Fornisce `richiestaAutenticata` per inviare il token JWT nell'header Authorization.
    Nota: le chiamate usano il prefisso `/api/v1` tramite il `calcolaRisorsa`.
*/
import { Injectable } from '@angular/core';
import { HttpClient, HttpHeaders, HttpErrorResponse } from '@angular/common/http';
import { concatMap, map, Observable, take, tap, catchError } from 'rxjs';
import { throwError } from 'rxjs';
import { ChiamataHTTP } from '../_type/chiamateHTTP.type';
import { IRrispostaServer } from '../_interfacce/IRispostaServer.interface';
import { UtilityService } from './utility.service';
import { AuthService } from './auth.service';
import { IFilm, ISerie, IEpisodio } from '../_models';
import { environment } from '../../environments/environment';

@Injectable({
    providedIn: 'root'
})
export class ApiService {

    constructor(private http: HttpClient, private authService: AuthService) { }

    /**
     * Costruisce l'URL completo dell'endpoint
     * @param risorsa array di segmenti dell'URL (es. ["accedi", "hashUtente"])
     * @returns stringa URL completa
     */
    protected calcolaRisorsa(risorsa: (string | number)[]): string {
        const server: string = environment.apiBase
        const versione: string = "v1"
        let url = server + "/" + versione + "/"
        // Unisce i segmenti della risorsa in un path unico
        // es: ['accedi','abcd'] -> '/api/v1/accedi/abcd'
        url = url + risorsa.join("/")
        return url
    }

    /**
     * Esegue una chiamata HTTP generica
     * @param risorsa array di segmenti dell'URL
     * @param tipo GET | POST | PUT | DELETE
     * @param parametri dati da inviare (solo per POST e PUT)
     * @returns Observable<IRrispostaServer>
     */
    protected richiestaGenerica(risorsa: (string | number)[], tipo: ChiamataHTTP, parametri: Object | null = null): Observable<IRrispostaServer> {

        const url = this.calcolaRisorsa(risorsa)

        let obs: Observable<IRrispostaServer>

        switch (tipo) {
            case "GET":
                obs = this.http.get<IRrispostaServer>(url)
                break

            case "POST":
                if (parametri !== null) {
                    obs = this.http.post<IRrispostaServer>(url, parametri)
                } else {
                    return throwError(() => new Error('NO_PARAMETRI'))
                }
                break

            case "PUT":
                if (parametri !== null) {
                    obs = this.http.put<IRrispostaServer>(url, parametri)
                } else {
                    return throwError(() => new Error('NO_PARAMETRI'))
                }
                break

            case "DELETE":
                obs = this.http.delete<IRrispostaServer>(url)
                break

            default:
                obs = this.http.get<IRrispostaServer>(url)
                break
        }

        // Centralizza mapping degli errori sia a livello di risposta server (campo `error`)
        // sia a livello HTTP (HttpErrorResponse). Trasforma in `Error` consistente.
        return obs.pipe(
            map((r: IRrispostaServer) => {
                if (r && r.error) {
                    // server returned structured error
                    const msg = typeof r.error === 'string' ? r.error : JSON.stringify(r.error)
                    throw new Error(msg)
                }
                return r
            }),
            catchError((err: any) => {
                // HttpErrorResponse oppure Error
                if (err instanceof HttpErrorResponse) {
                    const status = err.status
                    const statusText = err.statusText || ''
                    const serverMsg = err.error && err.error.message ? err.error.message : (typeof err.error === 'string' ? err.error : null)
                    const message = serverMsg ? `HTTP ${status} ${statusText}: ${serverMsg}` : `HTTP ${status} ${statusText}`
                    return throwError(() => new Error(message))
                }
                // already an Error or other
                const message = err?.message ?? String(err)
                return throwError(() => new Error(message))
            })
        )
    }

    /**
     * Chiamata pubblica (nessun controllo token): usa `richiestaGenerica`
     */
    public richiestaPubblica(risorsa: (string | number)[], tipo: ChiamataHTTP, parametri: Object | null = null): Observable<IRrispostaServer> {
        return this.richiestaGenerica(risorsa, tipo, parametri)
    }

    /**
     * Chiamata protetta: verifica che esista un token e poi esegue la richiesta.
     */
    public richiestaProtetta(risorsa: (string | number)[], tipo: ChiamataHTTP, parametri: Object | null = null): Observable<IRrispostaServer> {
        const tk = this.authService.getToken()
        if (!tk) {
            return throwError(() => new Error('NO_TOKEN'))
        }
        return this.richiestaGenerica(risorsa, tipo, parametri)
    }

    // -------------------- LOGIN 2 FASI --------------------

    /**
     * Fase 1 login: manda l'hash dell'utente, riceve il SALE dal server
     * @param hashUtente hash SHA512 dell'email
     */
    public getLoginFase1(hashUtente: string): Observable<IRrispostaServer> {
        const risorsa: string[] = ["accedi", hashUtente]
        return this.richiestaGenerica(risorsa, "GET")
    }

    /**
     * Fase 2 login: manda hash utente + password cifrata con il sale
     * @param hashUtente hash SHA512 dell'email
     * @param hashPassword SHA512 di (sale + password)
     */
    public getLoginFase2(hashUtente: string, hashPassword: string): Observable<IRrispostaServer> {
        const risorsa: string[] = ["accedi", hashUtente, hashPassword]
        return this.richiestaGenerica(risorsa, "GET")
    }

    /**
     * Login completo in 2 fasi:
     * 1. Chiama fase1 con hash dell'utente → riceve il sale
     * 2. Cifra (sale + password) e chiama fase2 → riceve il token JWT
     * @param utente email dell'utente
     * @param password password in chiaro
     */
    public login(utente: string, password: string): Observable<IRrispostaServer> {
        const hashUtente: string = UtilityService.hash(utente)

        return this.getLoginFase1(hashUtente).pipe(
            take(1),
            tap(x => console.log("FASE 1", x)),
            concatMap((rit: IRrispostaServer) => {
                // Il server risponde con il 'sale' da usare per nascondere la password
                const sale: string = rit.data?.sale ?? ''

                // Il client deve inviare SHA512(sale + SHA512(password)) per la fase2
                // qui `UtilityService.hash(password)` calcola SHA512(password)
                // `UtilityService.nascondiPassword` calcola SHA512(sale + passwordHash)
                const candidate: string = UtilityService.nascondiPassword(UtilityService.hash(password), sale)

                // Chiamata fase2 che restituirà il token JWT in caso di successo
                return this.getLoginFase2(hashUtente, candidate)
            })
        )
    }

    /**
     * Esegue una chiamata HTTP con il token JWT nell'header Authorization
     * @param risorsa array di segmenti dell'URL
     * @param tipo GET | POST | PUT | DELETE
     * @param tk token JWT
     * @param parametri dati da inviare (solo per POST e PUT)
     */
    // Nota: l'Authorization header è ora aggiunto automaticamente dall'AuthInterceptor.
    // Per compatibilità manteniamo i metodi generici `rchiestaGenerica` e wrapper specifici.


    //----------PUBLIC-------

    /**
     * Aggiungo l episodio id coi dati passati
     * @param number idSerie di appartenenza
     * @param FormData dati dell episodio
     * @return Observable<IRrispostaServer>
     */
    public aggiungiEpisodio(idSerie:number, dati: FormData): Observable<IRrispostaServer> {
        const risorsa: string[] = ["serieTV", idSerie.toString(), "episodi"];
        return this.richiestaProtetta(risorsa, "POST", dati)
    }
    // -----------------------------------------------------------------------
    /**
     * Modifica un episodio esistente
     * @param number idSerie serie di appartenenza
     * @param number idEpisodio episodio da modificare
     * @param Object dati campi da aggiornare
     */
    public modificaEpisodio(idSerie: number, idEpisodio: number, dati: Object): Observable<IRrispostaServer> {
        const risorsa: string[] = ["serieTV", idSerie.toString(), "episodi", idEpisodio.toString()];
        return this.richiestaProtetta(risorsa, "PUT", dati)
    }
    // -----------------------------------------------------------------------
    /**
     * @param FormData film da aggiungere
     * @returns Observable<IRrispostaServer>
     */
    public aggiungiFilm(dati: FormData): Observable<IRrispostaServer> {
        const risorsa: string[] = ["films"];
        return this.richiestaProtetta(risorsa, "POST", dati)
    }
    // -----------------------------------------------------------------------
    /**
     * Modifica un film esistente (multipart/form-data per supportare upload file)
     * @param id id del film da modificare
     * @param dati FormData con i campi da aggiornare (file inclusi)
     */
    public modificaFilm(id: number, dati: FormData): Observable<IRrispostaServer> {
        // Inviamo POST a /films/{id} con _method=PUT nel FormData (method spoofing Laravel)
        // perché PHP non supporta $_FILES con il metodo HTTP PUT
        const risorsa: string[] = ['films', id.toString()];
        return this.richiestaProtetta(risorsa, 'POST', dati);
    }
/*

    // -----------------------------------------------------------------------
    /**
     * @param FormData serieTV da aggiungere
     * @return Observable<IRrispostaServer>
     */
    public aggiungiSerieTV(dati: FormData): Observable<IRrispostaServer> {
        const risorsa: string[] = ["serieTV"];
        return this.richiestaProtetta(risorsa, "POST", dati)
    }
    /**
     * Modifica una serie TV esistente
     * @param number id della serie da modificare
     * @param Object dati campi da aggiornare
     */
    public modificaSerieTV(id: number, dati: Object): Observable<IRrispostaServer> {
        const risorsa: string[] = ["serieTV", id.toString()];
        return this.richiestaProtetta(risorsa, "PUT", dati)
    }

    /**
     * Modifica una serie TV con upload file (usa POST su endpoint /update)
     */
    public modificaSerieTVConFile(id: number, fd: FormData): Observable<IRrispostaServer> {
        // Inviamo POST a /serieTV/{id} con _method=PUT nel FormData (method spoofing Laravel)
        // perché PHP non supporta $_FILES con il metodo HTTP PUT
        const risorsa: string[] = ["serieTV", id.toString()];
        return this.richiestaProtetta(risorsa, "POST", fd);
    }
    /**
     * Wrapper pubblico per la registrazione utente
     */
    public registra(payload: any): Observable<IRrispostaServer> {
        const risorsa: string[] = ['registrazione'];
        return this.richiestaGenerica(risorsa, 'POST', payload);
    }
    // -----------------------------------------------------------------------
    /**
     * 
     * @returns Observable<ITrispostaserver>
     */
    public cercaUtente(utente: string): Observable<IRrispostaServer> {
        const hashUtente: string = UtilityService.hash(utente)
        const risorsa:string[] = ["searchMail", hashUtente]
        return this.richiestaGenerica(risorsa, "GET")
    }
    // -----------------------------------------------------------------------
    /**
     * elimina episodio
     * 
     * @param number idSerie di appartenenza
     * @param number id dell episodio da eliminare
     * @returns Observable<IRrispostaServer> vuota
     */
    public eliminaEpisodio(idSerie: number, idEpisodio: number): Observable<IRrispostaServer> {
        const risorsa: string[] = ["serieTV", idSerie.toString(), "episodi", idEpisodio.toString()];
        return this.richiestaProtetta(risorsa, "DELETE")
    }
    // -----------------------------------------------------------------------
    /**
     * elimina film
     * @param number id del film da eliminare
     * @returns Observable<IRrispostaServer> vuota
     */
    public eliminaFilm(id: number) {
        const risorsa: string[] = ["films", id.toString()];
        const rit = this.richiestaProtetta(risorsa, "DELETE")
        return rit;
    }
    // -----------------------------------------------------------------------
      /**
       * elimina serieTV
       * 
       * @param number id della serie da eliminare
       * @returns Observable<IRrispostaServer> vuota
       */
    public eliminaSerieTV(id: number) {
        const risorsa: string[] = ["serieTV", id.toString()];
        const rit = this.richiestaProtetta(risorsa, "DELETE")
        return rit;
    }

    // -----------------------------------------------------------------------
    /**
     * @returns Observable<IRrispostaServer>
     */
    public getCarousel(): Observable<IRrispostaServer> {
        // Wrapper dedicato per il carosello: usa l'endpoint `carousel` del backend
        const risorsa: string[] = ["carousel"];
        const rit = this.richiestaGenerica(risorsa, "GET")
        return rit;
    }
    /**
     * Wrapper tipizzato: ritorna direttamente l'array di `IFilm` per comodità nei componenti
     */
    public getCarouselItems(): Observable<IFilm[]> {
        return this.getCarousel().pipe(
            map(r => {
                if (r.error) throw r.error
                return (r.data as IFilm[]) ?? []
            })
        )
    }
    //-----------------------------------------------------------------------
    /**
     * @returns Observable<IRrispostaServer>
     */
    public getCategorie(): Observable<IRrispostaServer> {
        const risorsa: string[] = ["categorie"];
        const rit = this.richiestaGenerica(risorsa, "GET")
        return rit;
    }
    // -----------------------------------------------------------------------
    /**
     * 
     * @returns Observable<IRrispostaServer>
     */
    public getComuniItaliani(): Observable<IRrispostaServer> {
        const risorsa: string[] = ["comuni"];
        const rit = this.richiestaGenerica(risorsa, "GET")
        return rit;
    }
    // -----------------------------------------------------------------------
    /**
     * @returns Observable<IRrispostaServer>
     */
    public getConfigurazioni(): Observable<IRrispostaServer> {
        const risorsa: string[] = ["configurazioni"];
        const rit = this.richiestaGenerica(risorsa, "GET")
        return rit;
    }
    // -----------------------------------------------------------------------
    /**
     * ritorna i dati contatti o del contatto se specificato id
     * 
     * @param number | null id del contatto
     * @returns Observable<IRrispostaServer>
     */
    public getContatti(id: number | null = null, perPage: number | null = null): Observable<IRrispostaServer> {
        const risorsa: string[] = ["contatti"]
        if (id !== null) {
            risorsa.push(id.toString())
        }
        // Se si richiede la lista con un per_page specifico, costruisce l'URL con query param
        if (id === null && perPage !== null) {
            const url = this.calcolaRisorsa(risorsa) + `?per_page=${perPage}`
            return this.http.get<IRrispostaServer>(url).pipe(
                map((r: IRrispostaServer) => {
                    if (r && r.error) {
                        const msg = typeof r.error === 'string' ? r.error : JSON.stringify(r.error)
                        throw new Error(msg)
                    }
                    return r
                }),
                catchError((err: any) => {
                    if (err instanceof HttpErrorResponse) {
                        const serverMsg = err.error?.message ?? (typeof err.error === 'string' ? err.error : null)
                        const message = serverMsg ? `HTTP ${err.status}: ${serverMsg}` : `HTTP ${err.status}`
                        return throwError(() => new Error(message))
                    }
                    return throwError(() => new Error(err?.message ?? String(err)))
                })
            )
        }
        return this.richiestaGenerica(risorsa, "GET")
    }
    // -----------------------------------------------------------------------
    /**
     * @returns Observable<IRrispostaServer>
     */
    public getContattiRuoli(): Observable<IRrispostaServer> {
        const risorsa: string[] = ["contattiRuoli"]
        const rit = this.richiestaGenerica(risorsa, "GET")
        return rit;
    }
    // -----------------------------------------------------------------------
    /**
     * 
     * @returns Observable<IRrispostaServer>
     */
    public getContattiStati(): Observable<IRrispostaServer> {
        const risorsa: string[] = ["contattiStati"]
        const rit = this.richiestaGenerica(risorsa, "GET")
        return rit;
    }

    // -----------------------------------------------------------------------
    public modificaContatto(id: number, dati: Object): Observable<IRrispostaServer> {
        const risorsa: string[] = ["contatti", id.toString()]
        return this.richiestaGenerica(risorsa, "PUT", dati)
    }

    // -----------------------------------------------------------------------
    // Cambia la password dell'utente. Il backend riceve la password in chiaro,
    // calcola SHA512 internamente e invalida tutte le sessioni attive.
    public cambiaPassword(idContatto: number, currentPassword: string, newPassword: string): Observable<IRrispostaServer> {
        const risorsa: string[] = ['contatti', idContatto.toString(), 'change-password']
        return this.richiestaGenerica(risorsa, 'POST', { current_password: currentPassword, new_password: newPassword })
    }

    // -----------------------------------------------------------------------
    public eliminaContatto(id: number): Observable<IRrispostaServer> {
        const risorsa: string[] = ["contatti", id.toString()]
        return this.richiestaGenerica(risorsa, "DELETE")
    }

    // -----------------------------------------------------------------------
    public creaContatto(dati: Object): Observable<IRrispostaServer> {
        const risorsa: string[] = ["contatti"]
        return this.richiestaGenerica(risorsa, "POST", dati)
    }

    // -----------------------------------------------------------------------
    /**
     * ritorna i film tutti o gli ultimi [numero] inseriti
     * @param number | null numero degli ultimi dati inseriti (opzionale)
     * @return Observable<IRrispostaServer>
     */
    public getFilms(numero: number | null = null): Observable<IRrispostaServer> {
        const risorsa: string[] = ["films"]
        if (numero !== null) {
            risorsa.push('ultimi')
            risorsa.push(numero.toString())
        }
        const rit = this.richiestaGenerica(risorsa, "GET")
        return rit;

    }
    /**
     * Wrapper tipizzato per `getFilms` che mappa `data` su `IFilm[]`
     */
    public getFilmsItems(numero: number | null = null): Observable<IFilm[]> {
        return this.getFilms(numero).pipe(
            map(r => {
                if (r.error) throw r.error
                return (r.data as IFilm[]) ?? []
            })
        )
    }
    // -----------------------------------------------------------------------
    /**
     * ritorna la serie tv tutte o le ultime [numero] inserite
     * @param string categoria ricercata
     * @returns Observable<IRrispostaServer>
     */
    public getFilmsCategoria(categoria: string): Observable<IRrispostaServer> {
        const risorsa: string[] = ["films", "categoria", categoria]
        const rit = this.richiestaGenerica(risorsa, "GET")
        return rit;
    }
    // -----------------------------------------------------------------------
    /**
     * Ritorna i film filtrati per idCategoria (query param ?categoria=X)
     */
    public getFilmsPerCategoria(idCategoria: number): Observable<IFilm[]> {
        const url = this.calcolaRisorsa(['films']) + `?categoria=${idCategoria}`
        return this.http.get<IRrispostaServer>(url).pipe(
            map(r => (r.data as IFilm[]) ?? [])
        )
    }
    // -----------------------------------------------------------------------
    /**
     * Ricerca film per stringa (?search=X)
     */
    public searchFilms(query: string): Observable<IFilm[]> {
        const url = this.calcolaRisorsa(['films']) + `?search=${encodeURIComponent(query)}`
        return this.http.get<IRrispostaServer>(url).pipe(
            map(r => (r.data as IFilm[]) ?? [])
        )
    }
    // -----------------------------------------------------------------------
    /**
     * Ritorna il singolo film per id
     */
    public getFilmById(id: number): Observable<IFilm | null> {
        const risorsa: string[] = ["films", id.toString()]
        return this.richiestaGenerica(risorsa, "GET").pipe(
            map(r => (r.data as IFilm) ?? null)
        )
    }
    // -----------------------------------------------------------------------
    /**
     * ritorna i dati dell episodio [id]
     * @param number id dell episodio
     * @returns Observable<IRrispostaServer>
     */
    public getEpisodio(idSerie: number, idEpisodio: number): Observable<IRrispostaServer> {
        const risorsa: string[] = ["serieTV", idSerie.toString(), 'episodi', idEpisodio.toString()]
        const rit = this.richiestaGenerica(risorsa, "GET")
        return rit;
    }
    /**
     * Wrapper tipizzato per `getEpisodio` che mappa `data` su `IEpisodio` o null
     */
    public getEpisodioData(idSerie: number, idEpisodio: number): Observable<IEpisodio | null> {
        return this.getEpisodio(idSerie, idEpisodio).pipe(
            map(r => {
                if (r.error) throw r.error
                return (r.data as IEpisodio) ?? null
            })
        )
    }
    // -----------------------------------------------------------------------
    /**
     * 
     * @returns Observable<IRrispostaServer>
     */
    public getLingue(): Observable<IRrispostaServer> {
        const risorsa: string[] = ["lingue"]
        const rit = this.richiestaGenerica(risorsa, "GET")
        return rit;
    }
    //-----------------------------------------------------------------------
        /*
            Le funzioni `getLoginFase1` e `getLoginFase2` sono implementate
            più in alto nel file nella versione usata da `login()` (accettano
            hashUtente / hashPassword). Rimosse le duplicazioni.
        */
    // -----------------------------------------------------------------------
    /**
     * @return observable<IRrispostaServer>
     */
    public getNazioni(): Observable<IRrispostaServer> {
        const risorsa: string[] = ["nazioni"]
        const rit = this.richiestaGenerica(risorsa, "GET")
        return rit;
    }

    // -----------------------------------------------------------------------
    /**
     * 
     * @return Observable<IRrispostaServer>
     */
    public getProvince(): Observable<IRrispostaServer> {
        const risorsa: string[] = ["province"]
        const rit = this.richiestaGenerica(risorsa, "GET")
        return rit;
    }
    // -----------------------------------------------------------------------
    /**
     * @returns Observable<IRrispostaServer>
     */
    public getSerieTV(numero: number | null = null): Observable<IRrispostaServer> {
        const risorsa: string[] = ["serieTV"]
        if (numero !== null) {
            risorsa.push('ultimi')
            risorsa.push(numero.toString())
        }
        const rit = this.richiestaGenerica(risorsa, "GET")
        return rit;
    }

    /**
     * Wrapper tipizzato per `getSerieTV` che mappa `data` su `ISerie[]`
     */
    public getSerieTVItems(numero: number | null = null): Observable<ISerie[]> {
        return this.getSerieTV(numero).pipe(
            map(r => {
                if (r.error) throw r.error
                return (r.data as ISerie[]) ?? []
            })
        )
    }

    // -----------------------------------------------------------------------
    /**
     * ritorna la serie tv tutte o le ultime [numero] inserite
     * @param string categoria ricercata
     * @return Observable<IRrispostaServer>
     */
    public getSerieTVPerCategoria(categoria: string): Observable<IRrispostaServer> {
        const risorsa: string[] = ["serieTV", "categoria", categoria]
        const rit = this.richiestaGenerica(risorsa, "GET")
        return rit;
    }
    // -----------------------------------------------------------------------
    /**
     * ritorna il film
     * @param number id del film su DB
     * @return Observable<IRrispostaServer>
     */
    public getSingoloFilm(id: number): Observable<IRrispostaServer> {
        const risorsa: string[] = ["films", id.toString()]
        const rit = this.richiestaGenerica(risorsa, "GET")
        return rit;
    }
    /**
     * Wrapper tipizzato per `getSingoloFilm` che mappa `data` su `IFilm` o null
     */
    public getSingoloFilmData(id: number): Observable<IFilm | null> {
        return this.getSingoloFilm(id).pipe(
            map(r => {
                if (r.error) throw r.error
                return (r.data as IFilm) ?? null
            })
        )
    }
    // -----------------------------------------------------------------------
    /**
     * ritorna la serie TV
     * @param number id della serie su DB
     * @return Observable<IRrispostaServer>
     */
    public getSingolaSerieTV(id: number): Observable<IRrispostaServer> {
        const risorsa: string[] = ["serieTV", id.toString()]
        const rit = this.richiestaGenerica(risorsa, "GET")
        return rit;
    }
    /**
     * Wrapper tipizzato per `getSingolaSerieTV` che mappa `data` su `ISerie` o null
     */
    public getSingolaSerieTVData(id: number): Observable<ISerie | null> {
        return this.getSingolaSerieTV(id).pipe(
            map(r => {
                if (r.error) throw r.error
                return (r.data as ISerie) ?? null
            })
        )
    }

    /**
     * Raw GET /test-auth (protetto)
     */
    public getTestAuth(): Observable<IRrispostaServer> {
        const risorsa: string[] = ['test-auth'];
        return this.richiestaProtetta(risorsa, 'GET');
    }

    /**
     * Wrapper tipizzato per test-auth: ritorna il payload del token
     */
    public getTestAuthPayload(): Observable<any> {
        return this.getTestAuth().pipe(
            map(r => {
                if (r.error) throw r.error
                return r.data?.payload ?? null
            })
        )
    }
}
