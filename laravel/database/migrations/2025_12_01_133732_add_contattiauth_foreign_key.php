<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddContattiauthForeignKey extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Aggiunge FK per contattiauth.idContatto → contatti.idContatto
        Schema::table('contattiauth', function (Blueprint $table) {
            $table->foreign('idContatto')->references('idContatto')->on('contatti')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('contattiauth', function (Blueprint $table) {
            $table->dropForeign(['idContatto']);
        });
    }
}
