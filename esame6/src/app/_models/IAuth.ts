// Interfaccia che descrive la struttura dei dati di autenticazione salvati nello stato dell'app
export interface IAuth {
    idUtente: number | null;    // ID dell'utente loggato (null se non autenticato)
    idLingua: number;           // ID lingua preferita dell'utente
    idRuolo: number | null;     // ID del ruolo (1 = Amministratore, altri = Utente)
    idStato: number | null;     // Stato dell'account (es. attivo, sospeso)
    nome: string | null;        // Nome visualizzato nella navbar
    tk: string | null;          // Token JWT per le chiamate autenticate (null se non loggato)
    abilita: number[] | null;   // Array di permessi/abilità assegnati al ruolo
}
