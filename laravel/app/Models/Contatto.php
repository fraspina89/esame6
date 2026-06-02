<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use App\Models\ContattoRuolo;
use App\Models\ContattoStato;
use App\Models\Nazione;
use App\Models\ContattoIndirizzo;
use App\Models\ContattoRecapito;
use App\Models\ContattoCredito;
// use App\Models\Traits\ModelTrait; // Commentato temporaneamente

/**
 * Model: Contatto
 *
 * Rappresenta un contatto/utente dell'applicazione. Contiene relazioni con
 * ruoli, indirizzi, recapiti, crediti e stato. Utilizzato anche per
 * l'autenticazione (estende `Authenticatable`).
 *
 * Campi principali: `idContatto`, `nome`, `cognome`, `idContattoStato`.
 */
class Contatto extends Authenticatable
{
    use HasFactory, SoftDeletes;
    // use ModelTrait; // Commentato temporaneamente

    protected $table = 'contatti';
    protected $primaryKey = 'idContatto';

    protected $with = ['recapiti', 'indirizzi', 'crediti'];

    protected $fillable = [
        'idContattoStato', 
        'nome',
        'cognome',
        'sesso',
        'codiceFiscale',
        'partitaIva',
        'cittadinanza',
        'idNazioneNascita',
        'cittaNascita',
        'provinciaNascita',
        'dataNascita',
        'archiviato',
        'created_by',
        'updated_by'
    ];

    // -------------------------------------------------------------------------
    // PUBLIC
    // -------------------------------------------------------------------------

    /**
     * Aggiungi i ruoli per il contatto sulla tabella contatti_contattiRuoli
     *
     * @param integer $idContatto
     * @param string|array $idRuoli
     * @return Collection
     */
    public static function aggiungiContattoRuoli($idContatto, $idRuoli)
    {
        $contatto = Contatto::where('idContatto', $idContatto)->firstOrFail();
        if (is_string($idRuoli)) {
        $tmp = explode(',', $idRuoli);
    } else {
        $tmp = $idRuoli;
    }
    $contatto->ruoli()->attach($tmp);
    return $contatto->ruoli;
}

/**
 * @return mixed
 */
public function crediti()
{
    return $this->hasOne(ContattoCredito::class, 'idContatto', 'idContatto');
}

/**
 * Elimina i ruoli per il contatto sulla tabella contatti_contattiRuoli
 *
 * @param integer $idContatto
 * @param string|array $idRuoli
 * @return Collection
 */
public static function eliminaContattoRuoli($idContatto, $idRuoli)
{
    $contatto = Contatto::where('idContatto', $idContatto)->firstOrFail();
    if (is_string($idRuoli)) {
        $tmp = explode(',', $idRuoli);
    } else {
        $tmp = $idRuoli;
    }

    $contatto->ruoli()->detach($tmp);
    return $contatto->ruoli;
}

public function indirizzi()
{
    return $this->hasMany(ContattoIndirizzo::class, 'idContatto', 'idContatto')->orderBy('created_at', 'DESC');
}

public function recapiti()
{
    return $this->hasMany(ContattoRecapito::class, 'idContatto', 'idContatto')->orderBy('preferito', 'DESC');
}

/**
 * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
 */
public function stato()
{
    return $this->belongsTo(ContattoStato::class, 'idContattoStato', 'idContattoStato');
}

/**
 * Relazione Many-to-One: Un contatto appartiene a una nazione di nascita
 * 
 * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
 */
public function nazioneNascita()
{
    return $this->belongsTo(Nazione::class, 'idNazioneNascita', 'idNazione');
}

//---------------------------------------------------------
public function ruoli()
{
    return $this->belongsToMany(ContattoRuolo::class, 'contatti_contattiruolo', 'idContatto', 'idContattoRuolo');
}

//---------------------------------------------------------
/**
 * 
 * sincronizza i ruoli per il contatto sulla tabella contatti_contattiRuoli
 * 
 * @param integer $idContatto
 * @param string|array $idRuoli
 * @return Collection
 */

public static function sincronizzaContattoRuoli($idContatto, $idRuoli)
{
    $contatto = Contatto::where('idContatto', $idContatto)->firstOrFail();
    if (is_string($idRuoli)) {
        $tmp = explode(',', $idRuoli);
    } else {
        $tmp = $idRuoli;
    }
    $contatto->ruoli()->sync($tmp);
    return $contatto->ruoli;
}
}
