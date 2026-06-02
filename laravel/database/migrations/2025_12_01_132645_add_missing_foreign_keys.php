<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class AddMissingForeignKeys extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // 1. Aggiunge FK per contatti.idContattoStato → contattistati.idContattoStato (se non esiste)
        if (!$this->foreignKeyExists('contatti', 'fk_contatti_stato')) {
            Schema::table('contatti', function (Blueprint $table) {
                $table->foreign('idContattoStato', 'fk_contatti_stato')
                      ->references('idContattoStato')->on('contattistati')
                      ->onDelete('restrict')->onUpdate('cascade');
            });
        }
        
        // 2. Aggiunge FK per contatti.idNazioneNascita → nazioni.idNazione (se non esiste)
        if (!$this->foreignKeyExists('contatti', 'fk_contatti_nazione_nascita')) {
            Schema::table('contatti', function (Blueprint $table) {
                $table->foreign('idNazioneNascita', 'fk_contatti_nazione_nascita')
                      ->references('idNazione')->on('nazioni')
                      ->onDelete('set null')->onUpdate('cascade');
            });
        }
        
        // 3. Aggiunge FK per indirizzi.idTipologiaIndirizzo → tipiIndirizzo.idTipoIndirizzo (se non esiste)
        if (!$this->foreignKeyExists('indirizzi', 'fk_indirizzi_tipo_indirizzo')) {
            Schema::table('indirizzi', function (Blueprint $table) {
                $table->foreign('idTipologiaIndirizzo', 'fk_indirizzi_tipo_indirizzo')
                      ->references('idTipoIndirizzo')->on('tipiIndirizzo')
                      ->onDelete('restrict')->onUpdate('cascade');
            });
        }
        
        // 4. Aggiunge FK per recapiti.idTipoRecapito → tipiRecapito.idTipoRecapito (se non esiste)
        if (!$this->foreignKeyExists('recapiti', 'fk_recapiti_tipo_recapito')) {
            Schema::table('recapiti', function (Blueprint $table) {
                $table->foreign('idTipoRecapito', 'fk_recapiti_tipo_recapito')
                      ->references('idTipoRecapito')->on('tipiRecapito')
                      ->onDelete('restrict')->onUpdate('cascade');
            });
        }
    }
    
    /**
     * Verifica se una foreign key esiste già
     */
    private function foreignKeyExists($table, $constraintName)
    {
        $count = DB::select("
            SELECT COUNT(*) as count 
            FROM information_schema.TABLE_CONSTRAINTS 
            WHERE CONSTRAINT_SCHEMA = DATABASE() 
            AND TABLE_NAME = ? 
            AND CONSTRAINT_NAME = ? 
            AND CONSTRAINT_TYPE = 'FOREIGN KEY'
        ", [$table, $constraintName]);
        
        return $count[0]->count > 0;
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Rimuove le foreign key nell'ordine inverso
        Schema::table('recapiti', function (Blueprint $table) {
            $table->dropForeign('fk_recapiti_tipo_recapito');
        });
        
        Schema::table('indirizzi', function (Blueprint $table) {
            $table->dropForeign('fk_indirizzi_tipo_indirizzo');
        });
        
        Schema::table('contatti', function (Blueprint $table) {
            $table->dropForeign('fk_contatti_nazione_nascita');
        });
        
        Schema::table('contatti', function (Blueprint $table) {
            $table->dropForeign('fk_contatti_stato');
        });
    }
}
