<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Model: Film
 *
 * Rappresenta un film; include relazioni con `Categoria`, `Lingua`,
 * `Traduzione` e utilizza soft deletes.
 *
 * @property int $idFilm
 * @property int $idCategoria
 * @property string $titolo
 * @property string|null $descrizione
 * @property int|null $durata
 * @property string|null $regista
 * @property string|null $attori
 * @property int|null $anno
 * @property bool $visualizzato
 * @property string|null $locandina
 * @property string|null $carousel
 * @property string|null $video
 */
class Film extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'film';
    protected $primaryKey = 'idFilm';
    protected $keyType = 'int';
    public $incrementing = true;

    protected $fillable = [
        'idCategoria',
        'titolo',
        'descrizione',
        'durata',
        'regista',
        'attori',
        'anno',
        'visualizzato',
        'locandina',
        'carousel',
        'video'
    ];

    protected $casts = [
        'idCategoria' => 'integer',
        'durata' => 'integer',
        'anno' => 'integer',
        'visualizzato' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime'
    ];

    // Relazione con Categoria
    public function categoria()
    {
        return $this->belongsTo(Categoria::class, 'idCategoria', 'idCategoria');
    }

    // Accessor per la durata formattata (ore e minuti)
    public function getDurataFormattataAttribute()
    {
        if (!$this->durata) {
            return null;
        }
        
        $ore = intval($this->durata / 60);
        $minuti = $this->durata % 60;
        
        if ($ore > 0) {
            return $ore . 'h ' . $minuti . 'min';
        }
        
        return $minuti . 'min';
    }

    // Accessor per anno con suffisso
    public function getAnnoFormattatoAttribute()
    {
        return $this->anno ? $this->anno . '' : null;
    }
}
