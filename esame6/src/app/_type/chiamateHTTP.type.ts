// Tipo che limita i metodi HTTP accettati da ApiService.richiestaGenerica().
// Usato come parametro per evitare stringhe libere e garantire type-safety.
export type ChiamataHTTP = "GET" | "POST" | "PUT" | "DELETE" ;
