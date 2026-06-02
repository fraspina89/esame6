<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateContattiContattiRuoliTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('contatti_contattiruolo', function (Blueprint $table) {
            // PRIMARY KEY - id BIGINT(20) UNSIGNED AUTO_INCREMENT
            $table->id();
            
            // FOREIGN KEYS - BIGINT(20) UNSIGNED NOT NULL
            $table->unsignedBigInteger('idContatto');
            $table->unsignedBigInteger('idContattoRuolo');
            
            // TIMESTAMPS - TIMESTAMP NULL DEFAULT NULL
            $table->timestamps();
            
            // FOREIGN KEY CONSTRAINTS
            $table->foreign('idContatto')->references('idContatto')->on('contatti')->onDelete('cascade');
            $table->foreign('idContattoRuolo')->references('idContattoRuolo')->on('contattiruoli')->onDelete('cascade');
            
            // UNIQUE CONSTRAINT per evitare duplicati
            $table->unique(['idContatto', 'idContattoRuolo']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('contatti_contattiruolo');
    }
}
