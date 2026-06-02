<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Model: ContattoRecapito
 *
 * Rappresenta i recapiti di un contatto (telefono, email, ecc.) e le
 * informazioni di preferenza (es. `preferito`).
 */
class ContattoRecapito extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'recapiti';
    protected $primaryKey = 'idRecapito';

    protected $fillable = [
        'idContatto',
        'idTipoRecapito', 
        'valore',
        'descrizione',
        'preferito'
    ];

    protected $casts = [
        'preferito' => 'boolean',
        'deleted_at' => 'datetime',
    ];

    // -------------------------------------------------------------------------
    // RELAZIONI
    // -------------------------------------------------------------------------

    /**
     * Relazione Many-to-One: Un recapito appartiene a un contatto
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function contatto()
    {
        return $this->belongsTo(Contatto::class, 'idContatto', 'idContatto');
    }

    /**
     * Relazione Many-to-One: Un recapito appartiene a un tipo
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function tipoRecapito()
    {
        return $this->belongsTo(TipoRecapito::class, 'idTipoRecapito', 'idTipoRecapito');
    }

    // -------------------------------------------------------------------------
    // SCOPE E METODI UTILI
    // -------------------------------------------------------------------------

    /**
     * Scope per filtrare per contatto
     */
    public function scopeByContatto($query, $idContatto)
    {
        return $query->where('idContatto', $idContatto);
    }

    /**
     * Scope per filtrare per tipo
     */
    public function scopeByTipo($query, $idTipoRecapito)
    {
        return $query->where('idTipoRecapito', $idTipoRecapito);
    }

    /**
     * Scope per recapiti preferiti
     */
    public function scopePreferiti($query)
    {
        return $query->where('preferito', true);
    }

    /**
     * Scope per email
     */
    public function scopeEmail($query)
    {
        return $query->whereHas('tipoRecapito', function($q) {
            $q->where('nome', 'like', '%email%');
        });
    }

    /**
     * Scope per telefoni
     */
    public function scopeTelefoni($query)
    {
        return $query->whereHas('tipoRecapito', function($q) {
            $q->where('nome', 'like', '%telefono%');
        });
    }

    /**
     * Controlla se è un'email
     */
    public function isEmail()
    {
        return filter_var($this->valore, FILTER_VALIDATE_EMAIL) !== false;
    }

    /**
     * Controlla se è un numero di telefono
     */
    public function isTelefono()
    {
        return preg_match('/^[0-9+\-\s\(\)\.]{7,20}$/', $this->valore);
    }

    /**
     * Controlla se è un cellulare italiano
     */
    public function isCellulare()
    {
        return preg_match('/^3[0-9]{8,9}$/', str_replace([' ', '-', '.'], '', $this->valore));
    }

    /**
     * Formatta il numero di telefono
     */
    public function getValoreFormattatoAttribute()
    {
        if ($this->isTelefono()) {
            $numero = preg_replace('/[^0-9]/', '', $this->valore);
            
            if ($this->isCellulare()) {
                // Formato cellulare: 333 123 4567
                return preg_replace('/(\d{3})(\d{3})(\d{4})/', '$1 $2 $3', $numero);
            } else {
                // Formato fisso: 011 123456 o 02 12345678
                if (strlen($numero) == 9 && substr($numero, 0, 2) !== '02') {
                    return preg_replace('/(\d{3})(\d{6})/', '$1 $2', $numero);
                } else {
                    return preg_replace('/(\d{2})(\d+)/', '$1 $2', $numero);
                }
            }
        }
        
        return $this->valore;
    }
}