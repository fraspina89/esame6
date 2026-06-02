<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Model: Episodio
 *
 * Rappresenta un episodio di una serie TV; relazionato con `SerieTv`.
 */
class Episodio extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'episodi';
    protected $primaryKey = 'idEpisodio';

    protected $fillable = [
        'idSerie',
        'titolo',
        'descrizione',
        'numeroStagione',
        'numeroEpisodio',
        'durata',
        'anno',
        'idImmagine',
        'idFilmato'
    ];

    protected $casts = [
        'idSerie' => 'integer',
        'numeroStagione' => 'integer',
        'numeroEpisodio' => 'integer',
        'durata' => 'integer',
        'anno' => 'integer',
        'idImmagine' => 'integer',
        'idFilmato' => 'integer'
    ];

    protected $dates = [
        'deleted_at'
    ];

    // Scope query
    public function scopePerSerie($query, $serieId)
    {
        return $query->where('idSerie', $serieId);
    }

    public function scopePerStagione($query, $stagione)
    {
        return $query->where('numeroStagione', $stagione);
    }

    public function scopePerAnno($query, $anno)
    {
        return $query->where('anno', $anno);
    }

    public function scopeOrdinatoPerEpisodio($query)
    {
        return $query->orderBy('numeroStagione')->orderBy('numeroEpisodio');
    }

    public function scopeUltimi($query, $limite = 10)
    {
        return $query->orderBy('created_at', 'desc')->limit($limite);
    }

    // Accessori
    public function getCodiceEpisodioAttribute()
    {
        if ($this->numeroStagione && $this->numeroEpisodio) {
            return 'S' . str_pad($this->numeroStagione, 2, '0', STR_PAD_LEFT) . 
                   'E' . str_pad($this->numeroEpisodio, 2, '0', STR_PAD_LEFT);
        }
        return null;
    }

    public function getDurataFormattataAttribute()
    {
        if ($this->durata) {
            $ore = floor($this->durata / 60);
            $minuti = $this->durata % 60;
            
            if ($ore > 0) {
                return $ore . 'h ' . $minuti . 'm';
            }
            return $minuti . ' min';
        }
        return null;
    }

    public function getTitoloCompletoAttribute()
    {
        $codice = $this->codice_episodio;
        return $codice ? $codice . ' - ' . $this->titolo : $this->titolo;
    }

    // Metodi di supporto
    public function isPilot()
    {
        return $this->numeroStagione == 1 && $this->numeroEpisodio == 1;
    }

    public function isFinale()
    {
        // Controllo se è l'ultimo episodio della serie (logica semplificata)
        $ultimoEpisodio = static::perSerie($this->idSerie)
            ->orderBy('numeroStagione', 'desc')
            ->orderBy('numeroEpisodio', 'desc')
            ->first();
            
        return $ultimoEpisodio && 
               $ultimoEpisodio->idEpisodio === $this->idEpisodio;
    }

    // Relazioni
    public function serieTv()
    {
        return $this->belongsTo(SerieTv::class, 'idSerie', 'idSerie');
    }
}