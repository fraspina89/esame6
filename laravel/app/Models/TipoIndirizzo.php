<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Model: TipoIndirizzo
 *
 * Tipologie di indirizzo (residenza, domicilio, ecc.) usate dai contatti.
 */
class TipoIndirizzo extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'tipiindirizzo';
    protected $primaryKey = 'idTipoIndirizzo';

    protected $fillable = [
        'nome',
        'descrizione',
        'attivo'
    ];

    protected $casts = [
        'attivo' => 'boolean',
        'deleted_at' => 'datetime',
    ];

    // -------------------------------------------------------------------------
    // RELAZIONI
    // -------------------------------------------------------------------------

    /**
     * Relazione One-to-Many: Un tipo può avere molti indirizzi
     * 
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function indirizzi()
    {
        return $this->hasMany(ContattoIndirizzo::class, 'idTipoIndirizzo', 'idTipoIndirizzo');
    }

    // -------------------------------------------------------------------------
    // SCOPE E METODI UTILI
    // -------------------------------------------------------------------------

    /**
     * Scope per tipi attivi
     */
    public function scopeAttivi($query)
    {
        return $query->where('attivo', true);
    }

    /**
     * Scope per tipo residenza
     */
    public function scopeResidenza($query)
    {
        return $query->where('nome', 'like', '%residenza%');
    }

    /**
     * Scope per tipi aziendali
     */
    public function scopeAziendali($query)
    {
        return $query->where('nome', 'like', '%sede%')
                    ->orWhere('nome', 'like', '%lavoro%')
                    ->orWhere('nome', 'like', '%fatturazione%');
    }

    /**
     * Controlla se il tipo è per residenza
     */
    public function isResidenza()
    {
        return stripos($this->nome, 'residenza') !== false || 
               stripos($this->nome, 'domicilio') !== false;
    }

    /**
     * Controlla se il tipo è aziendale
     */
    public function isAziendale()
    {
        return stripos($this->nome, 'sede') !== false || 
               stripos($this->nome, 'lavoro') !== false ||
               stripos($this->nome, 'fatturazione') !== false;
    }

    /**
     * Controlla se il tipo è per spedizioni
     */
    public function isSpedizione()
    {
        return stripos($this->nome, 'spedizione') !== false ||
               stripos($this->nome, 'consegna') !== false;
    }
}