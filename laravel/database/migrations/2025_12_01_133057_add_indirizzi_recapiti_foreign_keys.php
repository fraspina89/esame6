<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddIndirizziRecapitiForeignKeys extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Aggiunge FK per indirizzi.idTipologiaIndirizzo → tipiIndirizzo.idTipoIndirizzo
        Schema::table('indirizzi', function (Blueprint $table) {
            $table->foreign('idTipologiaIndirizzo')->references('idTipoIndirizzo')->on('tipiIndirizzo')->onDelete('restrict');
        });
        
        // Aggiunge FK per recapiti.idTipoRecapito → tipiRecapito.idTipoRecapito
        Schema::table('recapiti', function (Blueprint $table) {
            $table->foreign('idTipoRecapito')->references('idTipoRecapito')->on('tipiRecapito')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('recapiti', function (Blueprint $table) {
            $table->dropForeign(['idTipoRecapito']);
        });
        
        Schema::table('indirizzi', function (Blueprint $table) {
            $table->dropForeign(['idTipologiaIndirizzo']);
        });
    }
}
