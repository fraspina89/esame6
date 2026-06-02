<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTipiIndirizzoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tipiIndirizzo', function (Blueprint $table) {
            $table->id('idTipoIndirizzo');
            $table->string('nome', 50)->unique();
            $table->string('descrizione')->nullable();
            $table->boolean('attivo')->default(true);
            $table->timestamps();
            $table->softDeletes();

            // Indici
            $table->index(['attivo']);
            $table->index(['nome']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tipiIndirizzo');
    }
}