<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RemoveSerieTvIndexes extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('serie_tv', function (Blueprint $table) {
            // Rimuove gli indici specificati (chiavi verdi)
            $table->dropIndex(['nome']); // 3
            $table->dropIndex(['annoInizio', 'annoFine']); // 9-10 (indice composito)
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('serie_tv', function (Blueprint $table) {
            // Ripristina gli indici se necessario
            $table->index('nome');
            $table->index(['annoInizio', 'annoFine']);
        });
    }
}
