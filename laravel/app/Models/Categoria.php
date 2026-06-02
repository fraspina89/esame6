<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Model: Categoria
 *
 * Rappresenta una categoria usata per classificare contenuti (es. film).
 * Contiene relazioni con `Film` e controlli per l'uso nelle request.
 */
class Categoria extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'categorie';
    protected $primaryKey = 'idCategoria';
    public $incrementing = false; // Disabilita AUTO_INCREMENT

    protected $fillable = [
        'nome',
        'visualizzato'
    ];

    protected $dates = [
        'deleted_at'
    ];
}

/**
 * @property int $idCategoria
 * @property string $nome
 * @property bool $visualizzato
 */