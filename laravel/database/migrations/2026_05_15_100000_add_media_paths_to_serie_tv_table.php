<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Sostituisce idImmagine/idFilmato (FK a tabelle media inesistenti)
 * con path diretti stringa, coerenti con il pattern della tabella film.
 * - locandina : percorso immagine piccola (poster)
 * - carousel  : percorso immagine grande (banner)
 * - video     : percorso mini filmato (trailer)
 */
class AddMediaPathsToSerieTvTable extends Migration
{
    public function up()
    {
        Schema::table('serie_tv', function (Blueprint $table) {
            $table->dropColumn(['idImmagine', 'idFilmato']);

            $table->string('locandina', 255)->nullable()->after('annoFine');
            $table->string('carousel',  255)->nullable()->after('locandina');
            $table->string('video',     255)->nullable()->after('carousel');
        });
    }

    public function down()
    {
        Schema::table('serie_tv', function (Blueprint $table) {
            $table->dropColumn(['locandina', 'carousel', 'video']);

            $table->unsignedInteger('idImmagine')->nullable();
            $table->unsignedInteger('idFilmato')->nullable();
        });
    }
}
