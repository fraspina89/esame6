<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RemoveContattiForeignKeys extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Rimuove tutte le foreign key dalla tabella contatti
        Schema::table('contatti', function (Blueprint $table) {
            // Prova prima con i nomi delle colonne
            try {
                $table->dropForeign(['idContattoStato']);
            } catch (Exception $e) {
                // Ignora se non esiste
            }
            
            try {
                $table->dropForeign(['idNazioneNascita']);
            } catch (Exception $e) {
                // Ignora se non esiste
            }
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Se necessario, si possono riaggiungere le FK
        Schema::table('contatti', function (Blueprint $table) {
            $table->foreign('idContattoStato')->references('idContattoStato')->on('contattistati')->onDelete('restrict');
            $table->foreign('idNazioneNascita')->references('idNazione')->on('nazioni')->onDelete('set null');
        });
    }
}
