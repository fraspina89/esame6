<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\ContattoAbilita;
use App\Models\Contatto;

/**
 * Model: ContattoRuolo
 *
 * Rappresenta i ruoli assegnabili ai contatti (es. Utente, Amministratore).
 * Gestisce la relazione Many-to-Many con i contatti.
 */
class ContattoRuolo extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'contattiruoli';
    protected $primaryKey = 'idContattoRuolo';

    protected $fillable = [
        'nome'
    ];

    // -------------------------------------------------------------------------
    // PUBLIC
    // -------------------------------------------------------------------------

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function abilita()
    {
        return $this->belongsToMany(ContattoAbilita::class, 'contattiruoli_contattiabilita', 'idContattoRuolo', 'idContattoAbilita');
    }

    /**
     * Aggiungi le abilità per il ruolo sulla tabella contattiRuoli_contattiAbilita
     *
     * @param integer $idRuolo
     * @param string|array $idAbilita
     * @return Collection
     */
    public static function aggiungiRuoloAbilita($idRuolo, $idAbilita)
    {
        $ruolo = ContattoRuolo::where('idContattoRuolo', $idRuolo)->firstOrFail();
        if (is_string($idAbilita)) {
            $tmp = explode(',', $idAbilita);
        } else {
            $tmp = $idAbilita;
        }

        $ruolo->abilita()->attach($tmp);
        return $ruolo->abilita;
    }
}