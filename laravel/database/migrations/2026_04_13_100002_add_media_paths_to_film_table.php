<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Sostituisce idImmagine/idFilmato (FK a tabelle media inesistenti)
 * con path diretti stringa, più semplici da usare nel seeder e nel frontend.
 * - locandina  : percorso immagine piccola (poster)
 * - carousel   : percorso immagine grande (banner)
 * - video      : percorso mini filmato (trailer)
 */
class AddMediaPathsToFilmTable extends Migration
{
    public function up()
    {
        Schema::table('film', function (Blueprint $table) {
            // Rimuove le FK integer non usate
            $table->dropColumn(['idImmagine', 'idFilmato']);

            // Aggiunge path stringa nullable
            $table->string('locandina', 255)->nullable()->after('anno');
            $table->string('carousel', 255)->nullable()->after('locandina');
            $table->string('video', 255)->nullable()->after('carousel');
        });
    }

    public function down()
    {
        Schema::table('film', function (Blueprint $table) {
            $table->dropColumn(['locandina', 'carousel', 'video']);

            $table->integer('idImmagine', false, true)->length(10)->nullable();
            $table->integer('idFilmato', false, true)->length(10)->nullable();
        });
    }
}
