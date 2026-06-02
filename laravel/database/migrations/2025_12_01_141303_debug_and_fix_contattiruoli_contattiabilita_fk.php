<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class DebugAndFixContattiruoliContattiabilitaFk extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Rimuovo tutti i foreign key esistenti
        $foreignKeys = collect(DB::select("
            SELECT CONSTRAINT_NAME 
            FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE 
            WHERE TABLE_NAME = 'contattiruoli_contattiabilita' 
            AND CONSTRAINT_SCHEMA = DATABASE()
            AND REFERENCED_TABLE_NAME IS NOT NULL
        "))->pluck('CONSTRAINT_NAME');
        
        foreach ($foreignKeys as $fkName) {
            DB::statement("ALTER TABLE contattiruoli_contattiabilita DROP FOREIGN KEY $fkName");
        }
        
        // Rimuovo tutti gli indici (tranne la primary key)
        $indexes = collect(DB::select("SHOW INDEX FROM contattiruoli_contattiabilita"))
            ->where('Key_name', '!=', 'PRIMARY')
            ->pluck('Key_name')
            ->unique();
            
        foreach ($indexes as $indexName) {
            DB::statement("ALTER TABLE contattiruoli_contattiabilita DROP INDEX $indexName");
        }
        
        // Aggiungo i foreign key corretti per entrambe le posizioni (2 e 3)
        Schema::table('contattiruoli_contattiabilita', function (Blueprint $table) {
            $table->foreign('idContattoAbilita', 'fk_contatti_abilita')->references('idContattoAbilita')->on('contattiabilita');
            $table->foreign('idContattoRuolo', 'fk_contatti_ruolo')->references('idContattoRuolo')->on('contattiruoli');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('contattiruoli_contattiabilita', function (Blueprint $table) {
            $table->dropForeign('fk_contatti_abilita');
            $table->dropForeign('fk_contatti_ruolo');
        });
    }
}
