/**
 * Interfaccia che descrive la struttura standard di ogni risposta del backend Laravel.
 * Tutti gli endpoint API restituiscono questo formato JSON:
 *   { "data": ..., "message": "...", "error": ... }
 * - data: i dati effettivi della risposta (es. array di film, oggetto utente)
 * - message: messaggio descrittivo dell'esito (es. "Film salvato con successo")
 * - error: eventuale errore (null se tutto è andato bene)
 */
export interface IRrispostaServer {
    data: any;              // Dati restituiti dal server
    message: string | null; // Messaggio informativo (può essere null)
    error: any;             // Dettagli errore (null se nessun errore)
}
