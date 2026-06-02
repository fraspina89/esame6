/*
    UtilityService
    - Contiene funzioni statiche utility usate dall'app (hash, gestione token, ecc.).
    - `hash`: calcola SHA512 di una stringa (usata per ottenere hash dell'email).
    - `nascondiPassword`: calcola SHA512(sale + password) usato nella procedura di login 2-fasi.
    - `leggiToken`: decodifica il JWT e ritorna il payload.
    Nota: le funzioni sono statiche per poterle usare senza istanziare il servizio.
*/
import { Injectable } from "@angular/core";
import { sha512 } from "js-sha512";
import { jwtDecode } from "jwt-decode";


@Injectable({ providedIn: 'root' })
export class UtilityService {

    /**
     * funzione che crea hash SHA512 di una stringa
     * @param str stringa da cifrare
     * @returns ritorna stringa cifrata
     */
    static hash(str: string): string {
        // Usa la libreria js-sha512 per ottenere l'hash della stringa
        const tmp = sha512(str)
        return tmp
    }

    /**
     * funzione che legge i dati dal token
     * @param token stringa che rappresenta il token
     * @returns ritorna un oggetto
     */
    static leggiToken(token: string): any {
        try {
            // Decodifica il JWT e ritorna il payload (header non incluso)
            return jwtDecode(token)
        } catch (error) {
            console.log("ERRORE DI LETTURA NEL TOKEN")
            return null
        }
    }

    /**
     * Funzione che calcola SHA512 della password legata al sale
     * @param password stringa che rappresenta la password
     * @param sale stringa da legare alla password
     * @returns stringa SHA512 della password unita al sale
     */
    static nascondiPassword(password: string, sale: string): string {
        // Concateno il sale davanti all'hash della password e ricalcolo SHA512
        // Il server applica lo stesso algoritmo: sha512(sale . pswDB)
        const tmp: string = sale + password
        const hash: string = sha512(tmp)
        return hash
    }
}
