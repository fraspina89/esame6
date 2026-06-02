<?php

namespace App\Http\Controllers\Api\V1;

use App\Helpers\AppHelper;
use App\Http\Controllers\Controller;
use App\Models\ContattoAuth;
use App\Models\ContattoPassword;
use App\Models\ContattoAccesso;
use App\Models\Configurazione;
use App\Models\ContattoSessione;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use Carbon\Carbon;


/**
 * Controller per l'autenticazione e la gestione delle sessioni
 *
 * Fornisce metodi per il login, verifica token, logout e gestione
 * delle credenziali associate ai contatti.
 */
class AccediController extends Controller
{
    /**
     * Cerco l hash dello user nel DB.
     *
     * @param string $utente
     * @param string $hash
    * @return mixed
     */
    public function searchMail($utente)
    {
        $tmp= (ContattoAuth::esisteUtente($utente)) ? true : false;
        return AppHelper::rispostaCustom($tmp);
    }

//--------------------------
/**
 * punto di ingresso del login
 * 
 * @param string $utente
 * @param string $hash
 * @return mixed
 */
    public function show($utente, $hash = null)
    {
       
        if ($hash == null) {
            return AccediController::controlloUtente($utente);
        } else {
            return AccediController::controlloPassword($utente, $hash);
        }
    }


    // ----------------------------------------------------------------------------- 
/**
 * Crea il token per sviluppo
 *
 * @return mixed
 */
public static function testToken()
{
    $utente    = hash("sha512", trim("Admin@Utente"));
    $password  = hash("sha512", trim("Password123!"));
    $sale      = hash("sha512", trim("Sale"));
    $sfida     = hash("sha512", trim("Sfida"));
    $secretJWT = hash("sha512", trim("Secret"));
    $auth = ContattoAuth::where('user', $utente)->firstOrFail();
    if ($auth != null) {
    $auth->inizioSfida = Carbon::now()->toDateTimeString();
        // $auth->sfida = $sfida;
        $auth->secretJWT = $secretJWT;
        $auth->save();

        $recordPassword = ContattoPassword::passwordAttuale($auth->idContatto);
        if ($recordPassword != null) {
            $recordPassword->sale = $sale;
            $recordPassword->psw  = $password;
            $recordPassword->save();
            $cipher = AppHelper::nascondiPassword($password, $sale);
            $tk = AppHelper::creaTokenSessione($auth->idContatto, $secretJWT);
            $dati = array("token" => $tk, "xLogin" => $cipher);
            $sessione = ContattoSessione::where('idContatto', $auth->idContatto)->firstOrFail();
            $sessione->token = $tk;
            $sessione->inizioSessione = time();
            $sessione->save();
            return AppHelper::rispostaCustom($dati);
        }
    }
}

//---------------------
/**
 * crea il token per sviluppo
 * 
 * @param string $utente
 * @return mixed
 */
public static function testLogin()
{
    $hashPassword = 'b109f3bbbc244eb82441917ed06d618b9008dd09b3befd1b5e07394c706a8bb980b1d7785e5976ec049b46df5f1326af5a2ea6d103fd07c95385ffab0cacbc86';
    $hashUtente = 'b109f3bbbc244eb82441917ed06d618b9008dd09b3befd1b5e07394c706a8bb980b1d7785e5976ec049b46df5f1326af5a2ea6d103fd07c95385ffab0cacbc86';
    return AccediController::controlloPassword($hashUtente, $hashPassword);
}

//-----------
/**
 * verifica il token ad ogni chiamata
 * 
 * @param string $token
 * @return object
 */
public static function verificaToken($token){

    $rit = null;
    $sessione = ContattoSessione::datiSessione($token);
    if ($sessione != null) {
    $inizioSessione = is_numeric($sessione->inizioSessione) ? intval($sessione->inizioSessione) : strtotime($sessione->inizioSessione);
    $durataSessione = (int) (Configurazione::leggiValore("durataSessione") ?? (15*24*60*60));
    $scadenzaSessione = $inizioSessione + $durataSessione;
    if (time() < $scadenzaSessione) {
        $auth = ContattoAuth::where('idContatto', $sessione->idContatto)->first();
        if ($auth != null) {
            $secretJWT = $auth->secretJWT;
            $payload = AppHelper::validaToken($token, $secretJWT, $sessione);
            if ($payload != null) {
                $rit = $payload;
            } else {
                abort (403, 'TK_0006');
            }
        } else {
            abort (403, 'TK_0005');
        }
    } else {
        abort (403, 'TK_0004');
    } 

} else {
    abort (403, 'TK_0003');

} return $rit;
}
// -----------------------------------------------------------------------------
/**
 * controllo validità utente
 * 
 * @param string $utente
 * @return mixed
 */
protected static function controlloUtente($utente) 
{
    //$sfida = hash('hash512', trim(Str::random(200)));
    $sale = hash('sha512', trim(Str::random(200)));
    if (ContattoAuth::esisteUtenteValidoPerLogin($utente)) {
       //esiste
        $auth = ContattoAuth::where('user', $utente)->first();
        // $auth->Sfida = $sfida;
        $auth->secretJWT = hash('sha512', trim(Str::random(200)));
        $auth->inizioSfida = Carbon::now()->toDateTimeString();
        $auth->save();
        $recordPassword = ContattoPassword::passwordAttuale($auth->idContatto);
        $recordPassword->sale = $sale;
        $recordPassword->save();
    } else {
        // non esiste, quindi invento la sfida e sale per confondere le idee
    }
    // $dati = array("sfida" => $sfida, "sale" => $sale);  
    $dati = array("sale" => $sale);
    return AppHelper::rispostaCustom($dati);
}
// -----------------------------------------------------------------------------
/**
 * punto di ingresso del login
 * 
 * @param string $utente
 * @param string $hash
 * @return mixed
 */

protected static function controlloPassword($utente, $hashClient) 
{
    if (ContattoAuth::esisteUtenteValidoPerLogin($utente)) {
        //esiste
        $auth = ContattoAuth::where('user', $utente)->first();
        //$sfida = $auth->sfida;
        $secretJWT = $auth->secretJWT;
        $inizioSfida = is_numeric($auth->inizioSfida) ? intval($auth->inizioSfida) : strtotime($auth->inizioSfida);
        $durataSfida = (int) (Configurazione::leggiValore("duratasfida") ?? 300);
        $maxTentativi = (int) (Configurazione::leggiValore("maxLoginErrati") ?? 5);
        $scadenzaSfida = $inizioSfida + $durataSfida ;
        if (time() < $scadenzaSfida) {
            $tentativi = ContattoAccesso::contaTentativi($auth->idContatto);
            if ($tentativi < $maxTentativi - 1) {
                // proseguo
                $recordPassword = ContattoPassword::passwordAttuale($auth->idContatto);
              
                $password = $recordPassword->psw;
                $sale     = $recordPassword->sale;
                $passwordNascostaDB= AppHelper::nascondiPassword($password, $sale);

                if ($hashClient == $passwordNascostaDB) {
                    //login corretto quindi creo token
                    $tk = AppHelper::creaTokenSessione($auth->idContatto, $secretJWT);
                    ContattoAccesso::eliminaTentativi($auth->idContatto);
                    $accesso = ContattoAccesso :: aggiungiAccesso($auth->idContatto);

                    ContattoSessione::eliminaSessione($auth->idContatto);
                    ContattoSessione::aggiornaSessione($auth->idContatto, $tk);

                    $dati = array('tk' => $tk);
                    return AppHelper::rispostaCustom($dati);               
                  
                } else {
    
                    ContattoAccesso::aggiungiTentativoFallito($auth->idContatto);
                    abort(403, 'ERR 004');
                }
            } else {
             
                abort(403, 'ERR 003');
            }
        } else {
            ContattoAccesso::aggiungiTentativoFallito($auth->idContatto);
            abort(403, 'ERR 002');
        }
    } else {
        abort(403, 'ERR 001');
    }
}

    /**
     * Logout: invalida la sessione corrente cancellando la sessione dal DB
     *
     * @param Request $request
    * @return mixed
     */
    public static function logout(Request $request)
    {
        $token = $request->bearerToken();
        if (!$token) {
            abort(403, 'TK_0001');
        }

        $sessione = ContattoSessione::datiSessione($token);
        if ($sessione != null) {
            ContattoSessione::eliminaSessione($sessione->idContatto);
            return AppHelper::rispostaCustom(true);
        } else {
            abort(403, 'TK_0003');
        }
    }

}