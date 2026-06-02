/*
    AuthService
    - Mantiene lo stato di autenticazione dell'app usando un `BehaviorSubject`.
    - Fornisce metodi per leggere/scrivere i dati `auth` su `localStorage` e per aggiornare
        il BehaviorSubject a cui i componenti si abbonano per ricevere aggiornamenti.
    - Centralizza le operazioni di login/logout lato client.
*/
import { Injectable } from '@angular/core';
import { BehaviorSubject } from 'rxjs';
import { Auth } from '../_type/auth.type';
import { UtilityService } from './utility.service';

@Injectable({
    providedIn: 'root'
})
export class AuthService {

    static auth: Auth
    private obsAuth$: BehaviorSubject<Auth>
    private logoutTimer: any | null = null

    constructor() {
        // All'avvio legge eventuale stato salvato in localStorage
        AuthService.auth = this.leggiAuthDaLocalStorage()

        // Il BehaviorSubject mantiene l'ultimo stato di `auth` e permette ai componenti
        // di sottoscriversi per ricevere aggiornamenti in tempo reale
        this.obsAuth$ = new BehaviorSubject<Auth>(AuthService.auth)
        // Se abbiamo un token valido, pianifica auto-logout alla scadenza
        if (this.isAuthenticated()) {
            this.scheduleAutoLogout()
        }
    }

    /**
     * Controlla se l'utente è autenticato e se il token è ancora valido.
     * Ritorna true se esiste un token valido, altrimenti pulisce lo stato e ritorna false.
     */
    isAuthenticated(): boolean {
        const auth = AuthService.auth
        if (!auth || !auth.tk) return false
        console.log('AuthService.isAuthenticated: token presente, verifico payload...')
        const payload = UtilityService.leggiToken(auth.tk)
        console.log('AuthService.isAuthenticated: payload=', payload)
        if (!payload) return false

        // Se presente campo exp nel token controlla la scadenza
        const exp = payload?.exp
        if (exp) {
            const now = Math.floor(Date.now() / 1000)
            if (exp < now) {
                // Token scaduto: pulisci lo stato
                this.cancellaAuthDaLocalStorage()
                this.settaObsAuth({
                    idLingua: 1,
                    tk: null,
                    nome: null,
                    idRuolo: null,
                    idStato: null,
                    abilita: null,
                    idUtente: null,
                    scadenzaPsw: null
                })
                return false
            }
        }

        return true
    }

    /**
     * Ritorna il payload decodificato del token corrente (o null)
     */
    getTokenPayload(): any | null {
        const auth = AuthService.auth
        if (!auth || !auth.tk) return null
        return UtilityService.leggiToken(auth.tk)
    }

    /**
     * Ritorna il token JWT corrente oppure null
     */
    getToken(): string | null {
        const auth = AuthService.auth
        return auth?.tk ?? null
    }

    /**
     * Restituisce il BehaviorSubject — i componenti si abbonano per ricevere aggiornamenti
     */
    leggiObsAuth(): BehaviorSubject<Auth> {
        return this.obsAuth$
    }

    /**
     * Aggiorna lo stato dell'autenticazione e notifica tutti i componenti abbonati
     */
    settaObsAuth(dati: Auth): void {
        AuthService.auth = dati
        this.obsAuth$.next(dati)
        // Quando lo stato cambia e contiene un token valido, (ri)configuriamo il timer
        this.clearAutoLogout()
        if (dati && dati.tk) {
            const payload = this.getTokenPayload()
            if (payload && payload.exp) {
                this.scheduleAutoLogout()
            }
        }
    }

    /**
     * Legge i dati di autenticazione dal localStorage (es. dopo un refresh della pagina)
     * Se non ci sono dati, restituisce un Auth "vuoto" (utente non loggato)
     */
    leggiAuthDaLocalStorage(): Auth {
        const tmp: string | null = localStorage.getItem("auth")
        if (tmp !== null) {
            // Ritorna l'oggetto Auth salvato in precedenza
            return JSON.parse(tmp)
        }
        return {
            idLingua: 1,
            tk: null,
            nome: null,
            idRuolo: null,
            idStato: null,
            abilita: null,
            idUtente: null,
            scadenzaPsw: null
        }
    }

    /**
     * Salva i dati di autenticazione nel localStorage
     */
    scriviAuthSuLocalStorage(auth: Auth): void {
        const tmp: string = JSON.stringify(auth)
        localStorage.setItem("auth", tmp)
        // (Ri)configuriamo il timer sulla scrittura
        this.clearAutoLogout()
        if (auth && auth.tk) this.scheduleAutoLogout()
    }

    /**
     * Cancella i dati di autenticazione (logout)
     */
    cancellaAuthDaLocalStorage(): void {
        localStorage.removeItem("auth")
        this.clearAutoLogout()
    }

    /**
     * Pianifica il logout automatico quando il token sta per scadere.
     * Pianifica il logout a (exp - now - margin) millisecondi.
     */
    private scheduleAutoLogout(marginSeconds: number = 5): void {
        try {
            const auth = AuthService.auth
            if (!auth || !auth.tk) return
            const payload = this.getTokenPayload()
            if (!payload || !payload.exp) return
            const nowSec = Math.floor(Date.now() / 1000)
            const expSec = payload.exp as number
            let diffSec = expSec - nowSec - marginSeconds
            if (diffSec <= 0) {
                // token già prossimo alla scadenza, esegui logout immediato
                this.performLogout()
                return
            }
            // trasforma in millisecondi
            const ms = diffSec * 1000
            this.logoutTimer = setTimeout(() => this.performLogout(), ms)
        } catch (e) {
            // ignora
        }
    }

    private clearAutoLogout(): void {
        if (this.logoutTimer) {
            clearTimeout(this.logoutTimer)
            this.logoutTimer = null
        }
    }

    /**
     * Esegue il logout centralizzato (cancella storage e notifica)
     */
    private performLogout(): void {
        try {
            this.cancellaAuthDaLocalStorage()
            this.settaObsAuth({
                idLingua: 1,
                tk: null,
                nome: null,
                idRuolo: null,
                idStato: null,
                abilita: null,
                idUtente: null,
                scadenzaPsw: null
            })
        } catch (e) {
            // ignora
        }
    }

    /**
     * Logout pubblico da utilizzare dall'app (es. Navbar, Interceptor)
     */
    logout(): void {
        try {
            // usa la routine privata per mantenere comportamento consistente
            this.performLogout()
        } catch (e) {
            // ignora
        }
    }
}
