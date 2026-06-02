<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Model: Traduzione
 *
 * Gestisce le traduzioni dei testi multilingua usate dall'applicazione.
 */
class Traduzione extends Model
{
    use HasFactory;

    protected $table = 'traduzioni';

    protected $fillable = [
        'lingua_id',
        'chiave',
        'valore',
        'gruppo'
    ];

    protected $casts = [
        'lingua_id' => 'integer'
    ];

    // Scopes
    public function scopePerLingua($query, $linguaId)
    {
        return $query->where('lingua_id', $linguaId);
    }

    public function scopePerChiave($query, $chiave)
    {
        return $query->where('chiave', $chiave);
    }

    public function scopePerGruppo($query, $gruppo)
    {
        return $query->where('gruppo', $gruppo);
    }

    // Helper methods
    public function getValoreFormattato($parametri = [])
    {
        $valore = $this->valore;
        
        // Sostituisce parametri placeholder se forniti
        foreach ($parametri as $key => $value) {
            $valore = str_replace(':' . $key, $value, $valore);
        }
        
        return $valore;
    }

    // Static methods
    public static function traduci($chiave, $linguaCodice = null, $parametri = [])
    {
        if (!$linguaCodice) {
            $lingua = Lingua::getPredefinita();
            if (!$lingua) {
                return $chiave; // fallback alla chiave se non trova lingua predefinita
            }
            $linguaId = $lingua->id;
        } else {
            $lingua = Lingua::getPerCodice($linguaCodice);
            if (!$lingua) {
                return $chiave; // fallback alla chiave se non trova la lingua
            }
            $linguaId = $lingua->id;
        }

        $traduzione = static::perLingua($linguaId)
            ->perChiave($chiave)
            ->first();

        if (!$traduzione) {
            return $chiave; // fallback alla chiave se non trova traduzione
        }

        return $traduzione->getValoreFormattato($parametri);
    }

    // Relazioni
    public function lingua()
    {
        return $this->belongsTo(Lingua::class);
    }
}