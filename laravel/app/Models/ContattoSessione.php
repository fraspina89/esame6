<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

/**
 * Model: ContattoSessione
 *
 * Memorizza le sessioni utente (token, inizioSessione, scadenza) e helper
 * per aggiornare/eliminare la sessione corrente.
 */
class ContattoSessione extends Model
{
    use HasFactory;
    protected $table = 'contattisessioni';
    protected $primaryKey = 'idContattoSessione';

    protected $fillable = [
        'idContatto',
        'token',
        'inizioSessione'
    ];

    // -------------------------------------------------------------------------
    // PUBLIC
    // -------------------------------------------------------------------------

    /**
     * Aggiorna la sessione per il contatto ed il token passato
     *
     * @param integer $idContatto
     * @param string $token
     */
    public static function aggiornaSessione($idContatto, $tk)
    {
        $where = ["idContatto" => $idContatto, "token" => $tk];
        $arr = ["inizioSessione" => time()];
        DB::table('contattisessioni')->updateOrInsert($where, $arr);
    }

    // -------------------------------------------------------------------------

    /**
     * Elimina la sessione per il contatto passato
     *
     * @param integer $idContatto
     */
    public static function eliminaSessione($idContatto)
    {
        DB::table('contattisessioni')->where('idContatto', $idContatto)->delete();
    }



//------
/**
 * dati sessione
 * 
 * @param string $token
 * @return \App\Models\ContattoSessione
 */
public static function datiSessione($token) {
    if ( ContattoSessione::esisteSessione($token) ) {
 return ContattoSessione::where('token', $token)->first();
    } else {
        return null;
    }
}
//------
/**
 * controlla se esiste la sessione per il token passato
 * 
 * @param string $token
 * @return boolean
 */
public static function esisteSessione($token) {
    return DB::table('contattisessioni')->where('token', $token)->exists();
}
}