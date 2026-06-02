<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Model: ContattoStato
 *
 * Rappresenta lo stato di un contatto (es. attivo, sospeso, cancellato).
 */
class ContattoStato extends Model
{
    use HasFactory, SoftDeletes;

    // Configurazione tabella e chiave primaria
    protected $table = 'contattistati'; // Nome tabella MySQL (minuscolo)
    protected $primaryKey = 'idContattoStato';

    // Campi fillable
    protected $fillable = [
        'nome'
    ];

    // -------------------------------------------------------------------------
    // RELAZIONI
    // -------------------------------------------------------------------------

    /**
     * Relazione One-to-Many: Un stato può avere molti contatti
     * 
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function contatti()
    {
        return $this->hasMany(Contatto::class, 'idContattoStato', 'idContattoStato');
    }
}
