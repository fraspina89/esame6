<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Model: Lingua
 *
 * Rappresenta una lingua supportata dall'app (codice, nome, ecc.).
 */
class Lingua extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'lingue';

    protected $fillable = [
        'codice',
        'nome',
        'nome_nativo',
        'bandiera',
        'attivo',
        'predefinita',
        'ordinamento'
    ];

    protected $casts = [
        'attivo' => 'boolean',
        'predefinita' => 'boolean',
        'ordinamento' => 'integer'
    ];

    protected $dates = [
        'deleted_at'
    ];

    // Scopes
    public function scopeAttivo($query)
    {
        return $query->where('attivo', true);
    }

    public function scopePredefinita($query)
    {
        return $query->where('predefinita', true);
    }

    public function scopeOrdinato($query)
    {
        return $query->orderBy('ordinamento')->orderBy('nome');
    }

    // Mutators
    public function setCodiceAttribute($value)
    {
        $this->attributes['codice'] = strtolower($value);
    }

    // Accessors
    public function getCodiceUpperAttribute()
    {
        return strtoupper($this->codice);
    }

    public function getNomeCompletoAttribute()
    {
        return $this->nome . ' (' . strtoupper($this->codice) . ')';
    }

    // Helper methods
    public function isPredefinita()
    {
        return $this->predefinita === true;
    }

    public function isAttiva()
    {
        return $this->attivo === true;
    }

    // Static methods
    public static function getPredefinita()
    {
        return static::predefinita()->first();
    }

    public static function getPerCodice($codice)
    {
        return static::where('codice', strtolower($codice))->first();
    }

    // Relazioni
    public function traduzioni()
    {
        return $this->hasMany(Traduzione::class);
    }

    public function traduzioniPerGruppo($gruppo)
    {
        return $this->traduzioni()->where('gruppo', $gruppo);
    }
}