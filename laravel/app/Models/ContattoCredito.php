<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Model: ContattoCredito
 *
 * Memorizza il credito disponibile per un contatto e log di movimenti.
 */
class ContattoCredito extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'crediti';
    protected $primaryKey = 'idCredito';

    protected $fillable = [
        'idContatto',
        'saldo',
        'limite',
        'attivo'
    ];

    protected $casts = [
        'saldo' => 'decimal:2',
        'limite' => 'decimal:2',
        'attivo' => 'boolean',
        'deleted_at' => 'datetime',
    ];

    // -------------------------------------------------------------------------
    // RELAZIONI
    // -------------------------------------------------------------------------

    /**
     * Relazione Many-to-One: Un credito appartiene a un contatto
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function contatto()
    {
        return $this->belongsTo(Contatto::class, 'idContatto', 'idContatto');
    }

    // -------------------------------------------------------------------------
    // SCOPE E METODI UTILI
    // -------------------------------------------------------------------------

    /**
     * Scope per crediti attivi
     */
    public function scopeAttivi($query)
    {
        return $query->where('attivo', true);
    }

    /**
     * Scope per crediti con saldo positivo
     */
    public function scopeConSaldo($query)
    {
        return $query->where('saldo', '>', 0);
    }

    /**
     * Scope per crediti con saldo negativo (debiti)
     */
    public function scopeInDebito($query)
    {
        return $query->where('saldo', '<', 0);
    }

    /**
     * Scope per crediti vicini al limite
     */
    public function scopeVicinoAlLimite($query, $soglia = 0.1)
    {
        return $query->whereRaw('ABS(saldo - limite) <= (limite * ?)', [$soglia]);
    }

    // -------------------------------------------------------------------------
    // METODI BUSINESS LOGIC
    // -------------------------------------------------------------------------

    /**
     * Aggiunge credito al saldo
     */
    public function aggiungiCredito($importo)
    {
        if ($importo <= 0) {
            throw new \InvalidArgumentException('L\'importo deve essere positivo');
        }

        $this->saldo += $importo;
        $this->save();

        return $this;
    }

    /**
     * Sottrae credito dal saldo
     */
    public function sottraiCredito($importo)
    {
        if ($importo <= 0) {
            throw new \InvalidArgumentException('L\'importo deve essere positivo');
        }

        $this->saldo -= $importo;
        $this->save();

        return $this;
    }

    /**
     * Imposta il saldo a un valore specifico
     */
    public function impostaSaldo($nuovoSaldo)
    {
        $this->saldo = $nuovoSaldo;
        $this->save();

        return $this;
    }

    /**
     * Verifica se il contatto può spendere un certo importo
     */
    public function puoSpendere($importo)
    {
        if (!$this->attivo) {
            return false;
        }

        $saldoDopoSpesa = $this->saldo - $importo;
        
        // Se c'è un limite, verifica che non venga superato (in negativo)
        if ($this->limite > 0) {
            return $saldoDopoSpesa >= -$this->limite;
        }

        // Se non c'è limite, può spendere solo se ha saldo positivo
        return $saldoDopoSpesa >= 0;
    }

    /**
     * Calcola la disponibilità totale (saldo + limite)
     */
    public function getDisponibilitaTotaleAttribute()
    {
        return $this->saldo + $this->limite;
    }

    /**
     * Verifica se è in debito
     */
    public function isInDebito()
    {
        return $this->saldo < 0;
    }

    /**
     * Verifica se ha saldo positivo
     */
    public function hasSaldoPositivo()
    {
        return $this->saldo > 0;
    }

    /**
     * Calcola la percentuale di utilizzo del limite
     */
    public function getPercentualeUtilizzoLimiteAttribute()
    {
        if ($this->limite <= 0) {
            return 0;
        }

        $utilizzo = abs($this->saldo < 0 ? $this->saldo : 0);
        return min(100, ($utilizzo / $this->limite) * 100);
    }

    /**
     * Formattazione del saldo per visualizzazione
     */
    public function getSaldoFormattatoAttribute()
    {
        return number_format($this->saldo, 2, ',', '.') . ' €';
    }

    /**
     * Formattazione del limite per visualizzazione
     */
    public function getLimiteFormattatoAttribute()
    {
        return number_format($this->limite, 2, ',', '.') . ' €';
    }
}