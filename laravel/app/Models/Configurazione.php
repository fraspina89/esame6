<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Model: Configurazione
 *
 * Rappresenta le impostazioni di configurazione dell'applicazione.
 * Ogni record contiene una chiave/valore usata per comportamento runtime.
 */
class Configurazione extends Model
{
    use HasFactory, SoftDeletes;

    // Specifica quale tabella del database rappresenta questo Model
    protected $table = 'configurazioni';
    
    // Specifica quale campo è la Primary Key
    protected $primaryKey = 'idConfigurazione';

    // Specifica quali campi possono essere "riempiti" via mass assignment
    protected $fillable = [
        'chiave',
        'valore'
    ];

    // -------------------------------------------------------------------------
    // METODI PERSONALIZZATI (come quello usato in AccediController)
    // -------------------------------------------------------------------------

    /**
     * Legge il valore di una configurazione dalla chiave
     * 
     * @param string $chiave
     * @return string|null
     */
    public static function leggiValore($chiave)
    {
        $configurazione = self::where('chiave', $chiave)->first();
        return $configurazione ? $configurazione->valore : null;
    }
}
