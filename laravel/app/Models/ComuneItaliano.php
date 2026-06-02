<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Model: ComuneItaliano
 *
 * Rappresenta un comune italiano con informazioni geografiche e codici.
 * Utilizzato per popolare dropdown e ricerche geografiche.
 *
 * @property int $idComune
 * @property string $comune
 * @property string|null $regione
 * @property string|null $provincia
 * @property string|null $sigla_provincia
 * @property string|null $cap
 */
class ComuneItaliano extends Model
{
    use HasFactory;

    protected $table = 'comuni_italiani';
    protected $primaryKey = 'idComune';

    protected $fillable = [
        'comune',
        'regione',
        'provincia',
        'zona',
        'sigla_provincia',
        'codice_istat',
        'abitanti',
        'superficie',
        'cap',
        'cap_finale',
        'cap_iniziale'
    ];

    protected $casts = [
        'abitanti' => 'integer',
        'superficie' => 'decimal:2',
        'cap_finale' => 'integer',
        'cap_iniziale' => 'integer'
    ];

    protected $dates = [
        'deleted_at'
    ];

    // Scopes
    public function scopeAttivo($query)
    {
        return $query->where('attivo', true);
    }

    public function scopePerRegione($query, $regione)
    {
        return $query->where('regione', $regione);
    }

    public function scopePerProvincia($query, $provincia)
    {
        return $query->where('provincia', $provincia);
    }

    public function scopePerSiglaProvincia($query, $sigla)
    {
        return $query->where('sigla_provincia', $sigla);
    }

    public function scopePerCap($query, $cap)
    {
        return $query->where('cap', $cap);
    }

    // Accessors
    public function getNomeCompletoAttribute()
    {
        return $this->comune . ' (' . $this->sigla_provincia . ')';
    }

    public function getLocalitaCompletaAttribute()
    {
        return $this->comune . ', ' . $this->provincia . ', ' . $this->regione;
    }

    // Helper methods
    public function isNord()
    {
        $regioniNord = ['Piemonte', 'Valle d\'Aosta/Vallée d\'Aoste', 'Lombardia', 'Trentino-Alto Adige/Südtirol', 
                       'Veneto', 'Friuli-Venezia Giulia', 'Liguria', 'Emilia-Romagna'];
        return in_array($this->regione, $regioniNord);
    }

    public function isCentro()
    {
        $regioniCentro = ['Toscana', 'Umbria', 'Marche', 'Lazio'];
        return in_array($this->regione, $regioniCentro);
    }

    public function isSud()
    {
        $regioniSud = ['Abruzzo', 'Molise', 'Campania', 'Puglia', 'Basilicata', 'Calabria'];
        return in_array($this->regione, $regioniSud);
    }

    public function isIsole()
    {
        $regioniIsole = ['Sicilia', 'Sardegna'];
        return in_array($this->regione, $regioniIsole);
    }

    // Relazioni (future)
    public function indirizzi()
    {
        return $this->hasMany(ContattoIndirizzo::class, 'comune_id');
    }
}