<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\TipoIndirizzo;

/**
 * Model: ContattoIndirizzo
 *
 * Rappresenta gli indirizzi associati a un contatto (residenza, domicilio).
 */
class ContattoIndirizzo extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'indirizzi';
    protected $primaryKey = 'idIndirizzo';

    protected $fillable = [
        'idContatto',
        'idTipologiaIndirizzo', 
        'idNazione',
        'cap',
        'comune',
        'indirizzo',
        'civico',
        'localita'
    ];

    protected $casts = [
        'deleted_at' => 'datetime',
    ];

    // -------------------------------------------------------------------------
    // RELAZIONI
    // -------------------------------------------------------------------------

    /**
     * Relazione Many-to-One: Un indirizzo appartiene a un contatto
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function contatto()
    {
        return $this->belongsTo(Contatto::class, 'idContatto', 'idContatto');
    }

    /**
     * Relazione Many-to-One: Un indirizzo appartiene a una nazione
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function nazione()
    {
        return $this->belongsTo(Nazione::class, 'idNazione', 'idNazione');
    }

    /**
     * Relazione Many-to-One: Un indirizzo appartiene a un tipo
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function tipoIndirizzo()
    {
        return $this->belongsTo(TipoIndirizzo::class, 'idTipoIndirizzo', 'idTipoIndirizzo');
    }

    // -------------------------------------------------------------------------
    // SCOPE E METODI UTILI
    // -------------------------------------------------------------------------

    /**
     * Scope per filtrare per contatto
     */
    public function scopeByContatto($query, $idContatto)
    {
        return $query->where('idContatto', $idContatto);
    }

    /**
     * Scope per filtrare per nazione
     */
    public function scopeByNazione($query, $idNazione)
    {
        return $query->where('idNazione', $idNazione);
    }

    /**
     * Scope per filtrare per comune
     */
    public function scopeByComune($query, $comune)
    {
        return $query->where('comune', 'like', "%{$comune}%");
    }

    /**
     * Accessor per indirizzo completo
     */
    public function getIndirizzoCompletoAttribute()
    {
        $parts = array_filter([
            $this->indirizzo,
            $this->civico,
            $this->localita,
            $this->cap . ' ' . $this->comune,
            $this->nazione ? $this->nazione->nome : null
        ]);
        
        return implode(', ', $parts);
    }
}
