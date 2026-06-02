<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFilmTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('film', function (Blueprint $table) {
            // 1. idFilm - BIGINT(20) UNSIGNED AUTO_INCREMENT PRIMARY KEY
            $table->bigInteger('idFilm', false, true)->length(20);
            
            // 2. idCategoria - TINYINT(3) UNSIGNED NOT NULL
            $table->tinyInteger('idCategoria', false, true)->length(3);
            
            // 3. titolo - VARCHAR(255) NOT NULL
            $table->string('titolo', 255);
            
            // 4. descrizione - TEXT NULL
            $table->text('descrizione')->nullable();
            
            // 5. durata - TINYINT(3) UNSIGNED NULL 
            $table->tinyInteger('durata', false, true)->length(3)->nullable();
            
            // 6. regista - VARCHAR(45) NULL
            $table->string('regista', 45)->nullable();
            
            // 7. attori - VARCHAR(45) NULL
            $table->string('attori', 45)->nullable();
            
            // 8. anno - SMALLINT(5) UNSIGNED NULL
            $table->smallInteger('anno', false, true)->length(5)->nullable();
            
            // 9. idImmagine - INT(10) UNSIGNED NULL
            $table->integer('idImmagine', false, true)->length(10)->nullable();
            
            // 10. idFilmato - INT(10) UNSIGNED NULL
            $table->integer('idFilmato', false, true)->length(10)->nullable();
            
            // 11-12-13. deleted_at, created_at, updated_at - TIMESTAMP NULL
            $table->timestamp('deleted_at')->nullable();
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();
            
            // Chiavi
            $table->primary('idFilm'); // Chiave gialla (primary)
            $table->foreign('idCategoria')->references('idCategoria')->on('categorie'); // Chiave verde (foreign)
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('film');
    }
}
