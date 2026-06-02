<?php

namespace App\Helpers;

use \Firebase\JWT\JWT;
use \Firebase\JWT\Key;
use App\Helpers\AesCtr;
use App\Models\Contatto;
use App\Models\Configurazione;
use App\Models\ContattoPassword;
use Illuminate\Support\Facades\DB;

class AppHelper
{
    // public---------

    /**
     * toglie il required alle rules di aggiornamento
     * 
     * @param array $rules
     * @return array
     */
    public static function aggiornaRegoleHelper($rules)
    {
        $newRules = array();
        foreach ($rules as $key => $value) {
            $newRules[$key] = str_ireplace('required|', '', $value);
        }
        return $newRules;
    }

    //--------------
    /**
     * unisci password e sale e fai HASH
     * 
     * @param string $testo da cifrare
     * @param string $chiave di cifratura
     * @return string 
     */
    public static function cifra($testo, $chiave)
    {
        $testoCifrato = AesCtr::encrypt($testo, $chiave, 256);
        return base64_decode($testoCifrato);
    }


    //-------------

    /**
     * estrae i nomi della tabella sul DB
     * 
     * @param string $tabella
     * @return array
     */
    public static function colonneTabellaDB($tabella){

        $SQL = "SELECT COLUMN_NAME FROM information_schema.columns WHERE table_schema='" . DB::connection()->getDatabaseName() . "' AND table_name='" . $tabella . "';";
        $tmp = DB::select($SQL);
        return $tmp;
    }

    /* ------------------------------------------------------------------------- */

    /**
     * Toglie il required alle rules di aggiornamento
     *
     * @param string $secretJWT come chiave di cifratura
     * @param integer $idContatto
     * @param integer $usaDa unixtime abilitazione uso token
     * @param integer $scade unixtime scadenza uso token
     * @return string
     */
    public static function creaTokenSessione($idContatto, $secretJWT, $usaDa = null, $scade = null)
    {
        $maxTime = 15 * 24 * 60 * 60; // il token scade sempre dopo 15gg max
        $recordContatto = Contatto::where('idContatto', $idContatto)->first();
        $t = time();
        $nbf = ($usaDa == null) ? $t : $usaDa;
        $exp = ($scade == null) ? $nbf + $maxTime : $scade;
        $ruolo = $recordContatto->ruoli->first();
        if ($ruolo === null) {
            abort(403, 'Contatto senza ruoli assegnati');
        }
        $idRuolo = $ruolo->idContattoRuolo;
        $abilita = $ruolo->abilita->toArray();
        $abilita = array_map(function ($arr) {
            return $arr['idContattoAbilita'];
        }, $abilita);

        $arr = array(
            "iss"  => "https://www.codex.it",
            "aud"  => null,
            "iat"  => $t,
            "nbf"  => $nbf,
            "exp"  => $exp,
            "data" => array(
                "idContatto" => $idContatto,
                "idContattoStato" => $recordContatto->idContattoStato,
                "idContattoRuolo" => $idRuolo,
                "abilita" => $abilita,
                "nome" => trim($recordContatto->nome . " " . $recordContatto->cognome)
            )
        );
        // Aggiungi timestamp di scadenza password se il record della password è disponibile
        try {
            $pswExpireAt = null;
            $recordPsw = ContattoPassword::where('idContatto', $idContatto)->orderBy('idContattoPassword', 'desc')->first();
            if ($recordPsw && isset($recordPsw->created_at)) {
                $created = is_numeric($recordPsw->created_at) ? intval($recordPsw->created_at) : strtotime($recordPsw->created_at);
                $soglia = (int) (Configurazione::leggiValore('soglia_scadenza_password') ?? 0);
                if ($soglia > 0) {
                    $pswExpireAt = $created + $soglia;
                }
            }
            if ($pswExpireAt !== null) {
                $arr['data']['scadenza_psw'] = $pswExpireAt;
            }
        } catch (\Exception $e) {
            // non blocchiamo la creazione del token per errori nel calcolo della scadenza
        }
        $token = JWT::encode($arr, $secretJWT, 'HS256');
        return $token;
    }

    /**
     * Unisci password e sale e fai HASH
     *
     * @param string $testoCifrato da decifrare
     * @param string $chiave usata per decifrare
     * @return string
     */
    public static function decifra($testoCifrato, $chiave)
    {
        $testoCifrato = base64_decode($testoCifrato);
        return AesCtr::decrypt($testoCifrato, $chiave, 256);
    }

    /* ------------------------------------------------------------------------- */

    /**
     * Unisci password e sale e fai HASH
     *
     * @param string $password
     * @param string $sale
     * @return string
     */
    public static function nascondiPassword($psw, $sale)
    {
        return hash("sha512", $sale . $psw);
    }

    /* ------------------------------------------------------------------------- */

    /**
     * Controlla se esiste l'utente passato
     *
     * @param boolean $successo TRUE se la richiesta è andata a buon fine
     * @param integer $codice STATUS code della richiesta
     * @param array $dati DATI richiesti
     * @param string $messaggio 
     * @param array $errori
     * @return array
     */
    public static function rispostaCustom($dati, $msg = null, $err = null)
    {
        $response = array();
        $response['data'] = $dati;
        if ($msg != null) $response['message'] = $msg;
        if ($err != null) $response['error'] = $err;
        return $response;
    }
    //------
    /**
     * 
     * valida token
     * 
     * @param string $token
     * @param string $messaggio
     * @param array $errori
     * @return object
     */
    public static function validaToken($token, $secretJWT, $sessione)
    {

        $rit = null;
        $payload = JWT::decode($token, new Key($secretJWT, 'HS256'));
        // Normalizza `inizioSessione` a timestamp (supporta epoch o datetime string)
        $sessioneInizio = null;
        if (isset($sessione->inizioSessione)) {
            if (is_numeric($sessione->inizioSessione)) {
                $sessioneInizio = intval($sessione->inizioSessione);
            } else {
                $sessioneInizio = strtotime($sessione->inizioSessione);
            }
        }
        // Se non abbiamo un valore di inizio sessione valido, rifiuta la validazione
        if ($sessioneInizio === false || $sessioneInizio === null) return $rit;

        // Controllo logico: il token deve essere stato emesso dopo (o al momento) dell'inizio della sessione
        if ($payload->iat >= $sessioneInizio) {
            if (isset($payload->data->idContatto) && $payload->data->idContatto == $sessione->idContatto) {
                $rit = $payload;
            }
        }
        return $rit;
    }
    //--------------------
}


// $payload = JWT::decode($token, $secretJWT,array ( 'HS256'));
