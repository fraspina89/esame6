<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Model: ContattoAccesso
 *
 * Log degli accessi di un contatto (IP, user agent, timestamp).
 */
class ContattoAccesso extends Model
{
    use HasFactory;
    protected $table = 'contattiaccessi';
    protected $primaryKey = 'id';

    protected $fillable = [
        'idContatto',
        'autenticato', 
        'ip'
    ];

    //--------PUBLIC------
    //--------
    /**
     * aggiungi tentatibvo fallito idContatto
     * 
     * @param integer $idContatto
     */
    public static function aggiungiAccesso($idContatto) {
        ContattoAccesso::eliminaTentativi($idContatto);
        return ContattoAccesso::nuovoRecord($idContatto, 1);
    }

    //---------
    /**
     * aggiungi tentatibvo fallito idContatto
     * 
     * @param string $idContatto
     */
    public static function aggiungiTentativoFallito($idContatto) {
        return ContattoAccesso::nuovoRecord($idContatto, 0);
}
//-------
/**
 * conta quanti tentativi per l idContatto sono registrati
 * 
 * @param integer $idContatto
 * @return integer
 */
public static function contaTentativi($idContatto) {
    $tmp = ContattoAccesso::where('idContatto', $idContatto)->where('autenticato', 0)->count();
    return $tmp;
}
//--------
/** 
 * conta quanti tentativi per l idContatto sono registrati
 */











//--------
/**
 * Elimina tutti i tentativi falliti per un contatto
 * 
 * @param integer $idContatto
 * @return void
 */
public static function eliminaTentativi($idContatto) {
    ContattoAccesso::where('idContatto', $idContatto)
                   ->where('autenticato', 0)
                   ->delete();
}

//----protected
/**
 * conta quanti tentativi per l idContatto sono registrati
 * 
 * @param string $idContatto
 * @param boolean $autenticato
 * @return \App\Models\Accesso
 */
protected static function nuovoRecord($idContatto, $autenticato) {
   $tmp= ContattoAccesso::create([
         'idContatto' => $idContatto,
         'autenticato' => $autenticato,
         'ip' => request()->ip()
   ]);
   return $tmp;
}
}