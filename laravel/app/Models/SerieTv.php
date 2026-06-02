<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Model: SerieTv
 *
 * Rappresenta una serie televisiva con relazioni verso episodi e traduzioni.
 */
class SerieTv extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'serie_tv';
    protected $primaryKey = 'idSerie';

    protected $fillable = [
        'idCategoria',
        'nome',
        'descrizione',
        'totaleStagioni',
        'numeroEpisodio',
        'regista',
        'attori',
        'annoInizio',
        'annoFine',
        'locandina',
        'carousel',
        'video',
    ];

    protected $casts = [
        'idCategoria' => 'integer',
        'totaleStagioni' => 'integer',
        'numeroEpisodio' => 'integer',
        'annoInizio' => 'integer',
        'annoFine' => 'integer',
    ];

    protected $dates = [
        'deleted_at'
    ];

    // Scope query
    public function scopePerCategoria($query, $categoriaId)
    {
        return $query->where('idCategoria', $categoriaId);
    }

    public function scopePerAnno($query, $anno)
    {
        return $query->where('annoInizio', '<=', $anno)
                    ->where(function($q) use ($anno) {
                        $q->where('annoFine', '>=', $anno)
                          ->orWhereNull('annoFine');
                    });
    }

    public function scopeRecenti($query, $limite = 10)
    {
        return $query->orderBy('annoInizio', 'desc')->limit($limite);
    }

    public function scopeUltimi($query, $numero)
    {
        return $query->orderBy('created_at', 'desc')->limit($numero);
    }

    // Accessori
    public function getPeriodoAttribute()
    {
        if ($this->annoFine) {
            return $this->annoInizio . '-' . $this->annoFine;
        }
        return $this->annoInizio . '-In corso';
    }

    public function getDurataAttribute()
    {
        if (!$this->annoFine) {
            return 'In corso dal ' . $this->annoInizio;
        }
        return ($this->annoFine - $this->annoInizio + 1) . ' anni';
    }

    public function getIsCompletaAttribute()
    {
        return !is_null($this->annoFine);
    }

    // Metodi di supporto
    public function isInCorso()
    {
        return is_null($this->annoFine);
    }

    public function isCompletata()
    {
        return !is_null($this->annoFine);
    }

    // Relazioni
    public function categoria()
    {
        return $this->belongsTo(Categoria::class, 'idCategoria');
    }

    public function episodi()
    {
        return $this->hasMany(Episodio::class, 'idSerie', 'idSerie');
    }
}