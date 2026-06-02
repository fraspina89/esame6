<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddContattiForeignKeys extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Aggiunge FK per contatti.idContattoStato → contattistati.idContattoStato
        Schema::table('contatti', function (Blueprint $table) {
            $table->foreign('idContattoStato')->references('idContattoStato')->on('contattistati')->onDelete('restrict');
        });
        
        // Aggiunge FK per contatti.idNazioneNascita → nazioni.idNazione
        Schema::table('contatti', function (Blueprint $table) {
            $table->foreign('idNazioneNascita')->references('idNazione')->on('nazioni')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('contatti', function (Blueprint $table) {
            $table->dropForeign(['idNazioneNascita']);
            $table->dropForeign(['idContattoStato']);
        });
    }
}
