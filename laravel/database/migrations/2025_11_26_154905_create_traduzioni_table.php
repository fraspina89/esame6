<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTraduzioniTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('traduzioni', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lingua_id')->constrained('lingue')->onDelete('cascade');
            $table->string('chiave', 100); // es: 'login.title', 'menu.home'
            $table->text('valore'); // traduzione effettiva
            $table->string('gruppo', 50)->nullable(); // es: 'menu', 'form', 'message'
            $table->timestamps();
            
            // Indici compositi per performance
            $table->unique(['lingua_id', 'chiave']);
            $table->index(['chiave', 'lingua_id']);
            $table->index(['gruppo', 'lingua_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('traduzioni');
    }
}
