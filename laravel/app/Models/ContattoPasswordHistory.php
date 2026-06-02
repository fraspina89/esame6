<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Model: ContattoPasswordHistory
 *
 * Storico delle password precedenti di un contatto per policy di sicurezza.
 */
class ContattoPasswordHistory extends Model
{
    use HasFactory;

    protected $table = 'contattipasswordhistory';
    protected $primaryKey = 'idContattoPasswordHistory';

    protected $fillable = [
        'idContatto',
        'psw',
        'sale'
    ];
}
