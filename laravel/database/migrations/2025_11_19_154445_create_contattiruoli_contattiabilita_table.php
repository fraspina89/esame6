<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateContattiruoliContattiabilitaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('contattiruoli_contattiabilita', function (Blueprint $table) {
            // PRIMARY KEY - id BIGINT(20) UNSIGNED AUTO_INCREMENT
            $table->id();
            
            // FOREIGN KEYS - BIGINT(20) UNSIGNED NOT NULL
            $table->unsignedBigInteger('idContattoRuolo');
            $table->unsignedBigInteger('idContattoAbilita');
            
            // TIMESTAMPS - TIMESTAMP NULL DEFAULT NULL
            $table->timestamps();
            
            // FOREIGN KEY CONSTRAINTS
            $table->foreign('idContattoRuolo')->references('idContattoRuolo')->on('contattiruoli')->onDelete('cascade');
            $table->foreign('idContattoAbilita')->references('idContattoAbilita')->on('contattiabilita')->onDelete('cascade');
            
            // UNIQUE CONSTRAINT per evitare duplicati (un ruolo non può avere la stessa abilità più volte)
            $table->unique(['idContattoRuolo', 'idContattoAbilita'], 'ruoli_abilita_unique');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('contattiruoli_contattiabilita');
    }
}
