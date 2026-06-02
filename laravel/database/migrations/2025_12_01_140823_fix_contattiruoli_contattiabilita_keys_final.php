<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class FixContattiruoliContattiabilitaKeysFinal extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('contattiruoli_contattiabilita', function (Blueprint $table) {
            // Rimuovo tutte le foreign key esistenti
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
        });
        
        // Ricreo la struttura corretta:
        // pos 2: idContattoAbilita con solo INDEX (non FK)
        // pos 3: idContattoRuolo con solo INDEX (non FK)
        Schema::table('contattiruoli_contattiabilita', function (Blueprint $table) {
            // Aggiungo solo gli indici semplici (non foreign key)
            $table->index('idContattoAbilita', 'idx_contatti_abilita');
            $table->index('idContattoRuolo', 'idx_contatti_ruolo');
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
            $table->dropIndex('idx_contatti_abilita');
            $table->dropIndex('idx_contatti_ruolo');
        });
    }
}
