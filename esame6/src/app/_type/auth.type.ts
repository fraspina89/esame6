/**
 * Tipo Auth — rappresenta lo stato di autenticazione corrente dell'app.
 * Viene salvato in localStorage e gestito da AuthService tramite un BehaviorSubject.
 * Nota: idRuolo === 1 significa Amministratore.
 */
export type Auth = {
    idLingua: number,        // Lingua preferita (default: 1 = italiano)
    tk: string | null,       // Token JWT (null se non autenticato)
    nome: string | null,     // Nome dell'utente da mostrare nella navbar
    idRuolo: number | null,  // Ruolo (1 = Amministratore, altri = Utente)
    idStato: number | null,  // Stato dell'account
    abilita: number [] | null, // Permessi del ruolo
    idUtente: number | null, // ID dell'utente nel database
    scadenzaPsw: number | null // Unix timestamp scadenza password (null = nessuna scadenza impostata)
}
