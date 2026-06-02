<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

/**
 * Model: ContattoAuth
 *
 * Rappresenta i metadati di autenticazione del contatto (es. user hash).
 */
class ContattoAuth extends Model
{
 use HasFactory;
    protected $table = 'contattiauth';
    protected $primaryKey = 'idContattoAuth';

    protected $fillable = [
        'idContatto',
        'user',
        'sfida',
        'secretJWT',
        'inizioSfida',
        'obbligoCambio',
    ];

    //--------PUBLIC------
    /**
     * Controlla se esiste l'utente passato
     * 
     * @param string $user
     * @return boolean
     */
    public static function esisteUtenteValidoPerLogin($user) {
        $tmp = DB::table('contatti')->join('contattiauth', 'contatti.idContatto', '=', 'contattiauth.idContatto')->where('contatti.idContattoStato', '=', 1)->where('contattiauth.user', '=', $user)->select('contattiauth.idContatto')->get()->count();
           return ($tmp > 0) ? true : false;
    }

    //------------
    /**
     * Controlla se esiste l'utente passato
     * 
     * @param string $user
     * @return boolean
     */
    public static function esisteUtente($user) 
    {
        $tmp = DB::table('contattiauth')->where('contattiauth.user', '=', $user)->select('contattiauth.idContatto')->get()->count();
           return ($tmp > 0) ? true : false;
    }
//--------------------
}