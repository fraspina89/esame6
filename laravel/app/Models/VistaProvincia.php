<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Model: VistaProvincia
 *
 * Vista che raggruppa informazioni sulle province usate nelle selezioni.
 */
class VistaProvincia extends Model
{
    // Non ha tabella fisica - è una vista derivata dai comuni
    protected $table = null;
    
    protected $fillable = [
        'nome',
        'regione',
        'sigla',
        'count_comuni'
    ];

    protected $casts = [
        'count_comuni' => 'integer'
    ];

    public $timestamps = false;

    /**
     * Ottiene tutte le province con statistiche dai comuni
     */
    public static function getAll()
    {
        return \App\Models\ComuneItaliano::selectRaw('
                provincia as nome,
                regione,
                sigla_provincia as sigla,
                COUNT(*) as count_comuni
            ')
            ->whereNotNull('provincia')
            ->where('provincia', '!=', '')
            ->groupBy('provincia', 'regione', 'sigla_provincia')
            ->orderBy('regione')
            ->orderBy('provincia')
            ->get()
            ->map(function ($item) {
                $provincia = new static();
                $provincia->nome = $item->nome;
                $provincia->regione = $item->regione;
                $provincia->sigla = $item->sigla;
                $provincia->count_comuni = $item->count_comuni;
                return $provincia;
            });
    }

    /**
     * Trova provincia per sigla
     */
    public static function findBySigla($sigla)
    {
        $result = \App\Models\ComuneItaliano::selectRaw('
                provincia as nome,
                regione,
                sigla_provincia as sigla,
                COUNT(*) as count_comuni
            ')
            ->where('sigla_provincia', $sigla)
            ->whereNotNull('provincia')
            ->where('provincia', '!=', '')
            ->groupBy('provincia', 'regione', 'sigla_provincia')
            ->first();

        if (!$result) {
            return null;
        }

        $provincia = new static();
        $provincia->nome = $result->nome;
        $provincia->regione = $result->regione;
        $provincia->sigla = $result->sigla;
        $provincia->count_comuni = $result->count_comuni;
        
        return $provincia;
    }

    /**
     * Ottiene province per regione
     */
    public static function perRegione($regione)
    {
        return \App\Models\ComuneItaliano::selectRaw('
                provincia as nome,
                regione,
                sigla_provincia as sigla,
                COUNT(*) as count_comuni
            ')
            ->where('regione', $regione)
            ->whereNotNull('provincia')
            ->where('provincia', '!=', '')
            ->groupBy('provincia', 'regione', 'sigla_provincia')
            ->orderBy('provincia')
            ->get()
            ->map(function ($item) {
                $provincia = new static();
                $provincia->nome = $item->nome;
                $provincia->regione = $item->regione;
                $provincia->sigla = $item->sigla;
                $provincia->count_comuni = $item->count_comuni;
                return $provincia;
            });
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

    /**
     * Relazione: comuni di questa provincia
     */
    public function comuni()
    {
        return \App\Models\ComuneItaliano::where('sigla_provincia', $this->sigla);
    }
}