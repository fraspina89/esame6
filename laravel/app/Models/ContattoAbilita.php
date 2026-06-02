<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Model: ContattoAbilita
 *
 * Rappresenta le abilità che possono essere assegnate ai ruoli dei contatti.
 * Include le relazioni verso i ruoli e la configurazione della tabella.
 */
class ContattoAbilita extends Model
{
    use HasFactory, SoftDeletes;

    // Configurazione tabella e chiave primaria
    protected $table = 'contattiabilita'; // Nome tabella MySQL (minuscolo)
    protected $primaryKey = 'idContattoAbilita';

    // Campi fillable
    protected $fillable = [
        'nome',
        'sku'
    ];

    // -------------------------------------------------------------------------
    // RELAZIONI
    // -------------------------------------------------------------------------

    /**
     * Relazione Many-to-Many: Un'abilità può essere assegnata a molti ruoli
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function ruoli()
    {
        return $this->belongsToMany(ContattoRuolo::class, 'contattiruoli_contattiabilita', 'idContattoAbilita', 'idContattoRuolo');
    }
}
