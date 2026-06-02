<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Model: TipoRecapito
 *
 * Tipologie di recapiti (telefono, cellulare, ecc.) utilizzate dai contatti.
 */
class TipoRecapito extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'tipirecapito';
    protected $primaryKey = 'idTipoRecapito';

    protected $fillable = [
        'nome',
        'descrizione',
        'validazione',
        'formato',
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
     * Relazione One-to-Many: Un tipo può avere molti recapiti
     * 
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function recapiti()
    {
        return $this->hasMany(ContattoRecapito::class, 'idTipoRecapito', 'idTipoRecapito');
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
     * Scope per tipo email
     */
    public function scopeEmail($query)
    {
        return $query->where('nome', 'like', '%email%');
    }

    /**
     * Scope per tipi telefono/cellulare
     */
    public function scopeTelefoni($query)
    {
        return $query->where('nome', 'like', '%telefono%')
                    ->orWhere('nome', 'like', '%cellulare%');
    }

    /**
     * Scope per tipo fax
     */
    public function scopeFax($query)
    {
        return $query->where('nome', 'like', '%fax%');
    }

    /**
     * Controlla se il tipo è per email
     */
    public function isEmail()
    {
        return stripos($this->nome, 'email') !== false;
    }

    /**
     * Controlla se il tipo è per telefono
     */
    public function isTelefono()
    {
        return stripos($this->nome, 'telefono') !== false || 
               stripos($this->nome, 'cellulare') !== false ||
               stripos($this->nome, 'mobile') !== false;
    }

    /**
     * Controlla se il tipo è per fax
     */
    public function isFax()
    {
        return stripos($this->nome, 'fax') !== false;
    }

    /**
     * Controlla se il tipo è per cellulare
     */
    public function isCellulare()
    {
        return stripos($this->nome, 'cellulare') !== false || stripos($this->nome, 'mobile') !== false;
    }

    /**
     * Restituisce il pattern di validazione per questo tipo
     */
    public function getPatternValidazione()
    {
        if ($this->isEmail()) {
            return 'email';
        } elseif ($this->isTelefono() || $this->isFax()) {
            return 'telefono';
        } else {
            return $this->validazione ?? 'string';
        }
    }

    /**
     * Restituisce il formato di esempio per questo tipo
     */
    public function getFormatoEsempio()
    {
        if ($this->formato) {
            return $this->formato;
        }

        if ($this->isEmail()) {
            return 'esempio@email.it';
        } elseif ($this->isTelefono()) {
            return '333 123 4567';
        } elseif ($this->isFax()) {
            return '011 123456';
        } else {
            return null;
        }
    }
}