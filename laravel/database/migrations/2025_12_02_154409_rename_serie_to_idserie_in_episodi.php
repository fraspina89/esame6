<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class RenameSerieToIdserieInEpisodi extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Prima rimuovo la FK esistente
        try {
            DB::statement("ALTER TABLE episodi DROP FOREIGN KEY fk_episodi_serie");
        } catch(Exception $e) {
            // FK potrebbe non esistere
        }
        
        // Rinomino la colonna da Serie a idSerie
        DB::statement("ALTER TABLE episodi CHANGE Serie idSerie BIGINT(20) UNSIGNED NOT NULL");
        
        // Ricreo la FK con il nuovo nome
        Schema::table('episodi', function (Blueprint $table) {
            $table->foreign('idSerie')->references('idSerie')->on('serie_tv');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Rollback: torno al nome originale
        Schema::table('episodi', function (Blueprint $table) {
            $table->dropForeign(['idSerie']);
        });
        
        DB::statement("ALTER TABLE episodi CHANGE idSerie Serie BIGINT(20) UNSIGNED NOT NULL");
        
        Schema::table('episodi', function (Blueprint $table) {
            $table->foreign('Serie')->references('idSerie')->on('serie_tv');
        });
    }
}
