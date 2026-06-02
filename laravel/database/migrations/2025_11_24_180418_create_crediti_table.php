<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCreditiTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('crediti', function (Blueprint $table) {
            $table->id('idCredito');
            $table->unsignedBigInteger('idContatto')->unique();
            $table->decimal('saldo', 10, 2)->default(0.00)->comment('Saldo attuale del credito');
            $table->decimal('limite', 10, 2)->default(0.00)->comment('Limite massimo di credito');
            $table->boolean('attivo')->default(true)->comment('Se il sistema di crediti è attivo per questo contatto');
            $table->timestamps();
            $table->softDeletes();

            // Foreign Key Constraints
            $table->foreign('idContatto')
                  ->references('idContatto')
                  ->on('contatti')
                  ->onUpdate('cascade')
                  ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('crediti');
    }
}
