<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSerieTvTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('serie_tv', function (Blueprint $table) {
            // 1. idSerie - BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT - PRIMARY KEY
            $table->id('idSerie');
            
            // 2. idCategoria - TINYINT(3) UNSIGNED NOT NULL - FOREIGN KEY 
            $table->unsignedTinyInteger('idCategoria');
            
            // 3. nome - VARCHAR(255) NOT NULL
            $table->string('nome', 255);
            
            // 4. descrizione - VARCHAR(255) NULL
            $table->string('descrizione', 255)->nullable();
            
            // 5. totaleStagioni - TINYINT(3) UNSIGNED NULL
            $table->unsignedTinyInteger('totaleStagioni')->nullable();
            
            // 6. numeroEpisodio - TINYINT(3) UNSIGNED NULL
            $table->unsignedTinyInteger('numeroEpisodio')->nullable();
            
            // 7. regista - VARCHAR(45) NULL
            $table->string('regista', 45)->nullable();
            
            // 8. attori - VARCHAR(45) NULL
            $table->string('attori', 45)->nullable();
            
            // 9. annoInizio - SMALLINT(5) UNSIGNED NULL
            $table->unsignedSmallInteger('annoInizio')->nullable();
            
            // 10. annoFine - SMALLINT(5) UNSIGNED NULL
            $table->unsignedSmallInteger('annoFine')->nullable();
            
            // 11. idImmagine - INT(10) UNSIGNED NULL
            $table->unsignedInteger('idImmagine')->nullable();
            
            // 12. idFilmato - INT(10) UNSIGNED NULL
            $table->unsignedInteger('idFilmato')->nullable();
            
            // 13. deleted_at - TIMESTAMP NULL
            $table->timestamp('deleted_at')->nullable();
            
            // 14-15. created_at, updated_at - TIMESTAMP NULL
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();
            
            // Foreign Key
            $table->foreign('idCategoria')->references('idCategoria')->on('categorie')->onDelete('cascade');
            
            // Indici
            $table->index('idCategoria');
            $table->index('nome');
            $table->index(['annoInizio', 'annoFine']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('serie_tv');
    }
}
