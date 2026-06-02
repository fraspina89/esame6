<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEpisodiTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('episodi', function (Blueprint $table) {
            // 1. idEpisodio - BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT - PRIMARY KEY
            $table->id('idEpisodio');
            
            // 2. Serie - BIGINT(20) UNSIGNED NOT NULL - FOREIGN KEY
            $table->unsignedBigInteger('Serie');
            
            // 3. titolo - VARCHAR(255) NOT NULL
            $table->string('titolo', 255);
            
            // 4. descrizione - VARCHAR(45) NULL
            $table->string('descrizione', 45)->nullable();
            
            // 5. numeroStagione - TINYINT(4) NULL
            $table->tinyInteger('numeroStagione')->nullable();
            
            // 6. numeroEpisodio - TINYINT(4) NULL
            $table->tinyInteger('numeroEpisodio')->nullable();
            
            // 7. durata - TINYINT(4) NULL (minuti)
            $table->tinyInteger('durata')->nullable();
            
            // 8. anno - SMALLINT(6) NULL
            $table->smallInteger('anno')->nullable();
            
            // 9. idImmagine - BIGINT(20) UNSIGNED NULL
            $table->unsignedBigInteger('idImmagine')->nullable();
            
            // 10. idFilmato - BIGINT(20) UNSIGNED NULL
            $table->unsignedBigInteger('idFilmato')->nullable();
            
            // 11. deleted_at - TIMESTAMP NULL
            $table->timestamp('deleted_at')->nullable();
            
            // 12-13. created_at, updated_at - TIMESTAMP NULL
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();
            
            // Foreign Key - collego alla tabella serie_tv
            $table->foreign('Serie')->references('idSerie')->on('serie_tv')->onDelete('cascade');
            
            // Indici
            $table->index('Serie');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('episodi');
    }
}
