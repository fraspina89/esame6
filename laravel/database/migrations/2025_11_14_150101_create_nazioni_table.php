<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateNazioniTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('nazioni', function (Blueprint $table) {
            // 1. idNazione - BIGINT(20) UNSIGNED PRIMARY KEY AUTO_INCREMENT
            $table->bigIncrements('idNazione');
            
            // 2. nome - VARCHAR(45) NOT NULL, utf8mb4_unicode_ci
            $table->string('nome', 45)->nullable(false)->collation('utf8mb4_unicode_ci');
            
            // 3. continente - VARCHAR(45) NOT NULL, utf8mb4_unicode_ci
            $table->string('continente', 45)->nullable(false)->collation('utf8mb4_unicode_ci');
            
            // 4. iso - CHAR(2) NOT NULL, utf8mb4_unicode_ci
            $table->char('iso', 2)->nullable(false)->collation('utf8mb4_unicode_ci');
            
            // 5. iso3 - CHAR(3) NOT NULL, utf8mb4_unicode_ci
            $table->char('iso3', 3)->nullable(false)->collation('utf8mb4_unicode_ci');
            
            // 6. prefissoTelefonico - VARCHAR(45) NOT NULL, utf8mb4_unicode_ci
            $table->string('prefissoTelefonico', 45)->nullable(false)->collation('utf8mb4_unicode_ci');
            
            // 7-8. timestamps - TIMESTAMP NULL
            $table->timestamps();
            
            // Indici per performance
            $table->unique('iso', 'idx_nazioni_iso');
            $table->unique('iso3', 'idx_nazioni_iso3');
            $table->index('continente', 'idx_nazioni_continente');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('nazioni');
    }
}
