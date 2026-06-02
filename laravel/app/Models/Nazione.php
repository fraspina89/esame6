<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Model: Nazione
 *
 * Rappresenta una nazione con i suoi attributi (nome, codice, ecc.).
 */
class Nazione extends Model
{
    use HasFactory;

    protected $table = 'nazioni';
    protected $primaryKey = 'idNazione';

    protected $fillable = [
        'nome',
        'continente',
        'iso',
        'iso3',
        'prefissoTelefonico'
    ];

    // -------------------------------------------------------------------------
    // RELAZIONI
    // -------------------------------------------------------------------------

    /**
     * Relazione One-to-Many: Una nazione può avere molti contatti nati in essa
     * 
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function contatti()
    {
        return $this->hasMany(Contatto::class, 'idNazioneNascita', 'idNazione');
    }

    // -------------------------------------------------------------------------
    // SCOPE E METODI UTILI
    // -------------------------------------------------------------------------

    /**
     * Scope per cercare per codice ISO
     */
    public function scopeByIso($query, $iso)
    {
        return $query->where('iso', $iso);
    }

    /**
     * Scope per cercare per codice ISO3
     */
    public function scopeByIso3($query, $iso3)
    {
        return $query->where('iso3', $iso3);
    }

    /**
     * Scope per filtrare per continente
     */
    public function scopeByContinente($query, $continente)
    {
        return $query->where('continente', $continente);
    }

    /**
     * Accessor per formato completo
     */
    public function getFormatoCompletoAttribute()
    {
        return "{$this->nome} ({$this->iso})";
    }
}
