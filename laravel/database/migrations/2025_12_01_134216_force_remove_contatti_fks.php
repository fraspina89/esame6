<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class ForceRemoveContattiFks extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Prima trova tutti i nomi delle foreign key sulla tabella contatti
        $foreignKeys = DB::select("
            SELECT CONSTRAINT_NAME 
            FROM information_schema.KEY_COLUMN_USAGE 
            WHERE TABLE_SCHEMA = DATABASE() 
            AND TABLE_NAME = 'contatti' 
            AND REFERENCED_TABLE_NAME IS NOT NULL
        ");
        
        // Rimuove ogni foreign key trovata
        foreach ($foreignKeys as $fk) {
            DB::statement("ALTER TABLE contatti DROP FOREIGN KEY {$fk->CONSTRAINT_NAME}");
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Non serve rollback - le FK erano state aggiunte per errore
    }
}
