<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Model: VistaTraduzioni
 *
 * Rappresenta una vista per le traduzioni aggregate usata nelle liste.
 */
class VistaTraduzioni extends Model
{
    // Non ha tabella fisica - è una vista derivata dalle traduzioni
    protected $table = null;
    
    protected $fillable = [
        'lingua_id',
        'lingua_codice',
        'chiave',
        'valore',
        'gruppo'
    ];

    public $timestamps = false;

    /**
     * Ottiene tutte le traduzioni per una lingua
     */
    public static function perLingua($linguaId)
    {
        $traduzioni = Traduzione::with('lingua')
            ->where('lingua_id', $linguaId)
            ->orderBy('gruppo')
            ->orderBy('chiave')
            ->get();

        return $traduzioni->map(function ($traduzione) {
            $vista = new static();
            $vista->lingua_id = $traduzione->lingua_id;
            $vista->lingua_codice = $traduzione->lingua->codice;
            $vista->chiave = $traduzione->chiave;
            $vista->valore = $traduzione->valore;
            $vista->gruppo = $traduzione->gruppo;
            return $vista;
        });
    }

    /**
     * Ottiene una singola traduzione per lingua e chiave
     */
    public static function perLinguaEChiave($linguaId, $chiave)
    {
        $traduzione = Traduzione::with('lingua')
            ->where('lingua_id', $linguaId)
            ->where('chiave', $chiave)
            ->first();

        if (!$traduzione) {
            return null;
        }

        $vista = new static();
        $vista->lingua_id = $traduzione->lingua_id;
        $vista->lingua_codice = $traduzione->lingua->codice;
        $vista->chiave = $traduzione->chiave;
        $vista->valore = $traduzione->valore;
        $vista->gruppo = $traduzione->gruppo;
        
        return $vista;
    }

    /**
     * Ottiene traduzioni raggruppate per gruppo
     */
    public static function raggruppatePer($linguaId)
    {
        $traduzioni = static::perLingua($linguaId);
        
        return $traduzioni->groupBy('gruppo')->map(function ($gruppo) {
            return $gruppo->mapWithKeys(function ($traduzione) {
                return [$traduzione->chiave => $traduzione->valore];
            });
        });
    }
}