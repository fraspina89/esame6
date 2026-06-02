<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLingueTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('lingue', function (Blueprint $table) {
            $table->id();
            $table->string('codice', 5)->unique(); // es: 'it', 'en', 'fr', 'es'  
            $table->string('nome', 50); // es: 'Italiano', 'English', 'Français'
            $table->string('nome_nativo', 50); // es: 'Italiano', 'English', 'Français' 
            $table->string('bandiera', 10)->nullable(); // codice emoji bandiera
            $table->boolean('attivo')->default(true);
            $table->boolean('predefinita')->default(false);
            $table->integer('ordinamento')->default(0);
            $table->timestamps();
            $table->softDeletes();
            
            // Indici
            $table->index(['attivo', 'ordinamento']);
            $table->index('predefinita');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('lingue');
    }
}
