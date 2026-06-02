<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class CleanNazioniKeys extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Rimuovo tutte le foreign key dalla tabella nazioni
        $foreignKeys = collect(DB::select("
            SELECT CONSTRAINT_NAME 
            FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE 
            WHERE TABLE_NAME = 'nazioni' 
            AND CONSTRAINT_SCHEMA = DATABASE()
            AND REFERENCED_TABLE_NAME IS NOT NULL
        "))->pluck('CONSTRAINT_NAME');
        
        foreach ($foreignKeys as $fkName) {
            DB::statement("ALTER TABLE nazioni DROP FOREIGN KEY $fkName");
        }
        
        // Rimuovo tutti gli indici (tranne la primary key)
        $indexes = collect(DB::select("SHOW INDEX FROM nazioni"))
            ->where('Key_name', '!=', 'PRIMARY')
            ->pluck('Key_name')
            ->unique();
            
        foreach ($indexes as $indexName) {
            DB::statement("ALTER TABLE nazioni DROP INDEX $indexName");
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Non ricreo niente, la tabella nazioni dovrebbe avere solo la PK
    }
}
