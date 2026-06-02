<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateComuniItaliaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('comuni_italia', function (Blueprint $table) {
            $table->id();
            $table->string('nome', 100)->index();
            $table->string('regione', 50)->index();
            $table->string('provincia', 50)->nullable()->index();
            $table->string('sigla_provincia', 2)->nullable()->index();
            $table->string('codice_catastale', 4)->unique();
            $table->string('cap', 5)->index();
            $table->boolean('attivo')->default(true);
            $table->integer('ordinamento')->default(0);
            $table->timestamps();
            $table->softDeletes();
            
            // Indici compositi per performance
            $table->index(['regione', 'provincia']);
            $table->index(['nome', 'provincia']);
            $table->index(['cap', 'provincia']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('comuni_italia');
    }
}
