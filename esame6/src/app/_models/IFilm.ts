// Interfaccia che rappresenta una categoria di film (es. Azione, Commedia...)
export interface ICategoria {
  idCategoria: number; // ID univoco della categoria
  nome: string;        // Nome della categoria
}

// Interfaccia che rappresenta un singolo film nel catalogo
export interface IFilm {
  idFilm: number;         // ID univoco del film nel database
  idCategoria: number;    // ID della categoria a cui appartiene il film
  titolo: string;         // Titolo del film (obbligatorio)
  descrizione?: string;   // Trama / sinossi del film
  anno?: number;          // Anno di uscita
  durata?: number;        // Durata in minuti
  regista?: string;       // Nome del regista (restituito solo agli utenti autenticati)
  attori?: string;        // Lista attori (restituita solo agli utenti autenticati)
  locandina?: string;     // Percorso immagine locandina
  carousel?: string;      // Percorso immagine per il carosello
  video?: string;         // Percorso del file video
  categoria?: ICategoria; // Oggetto categoria popolato dalla relazione Laravel
}
