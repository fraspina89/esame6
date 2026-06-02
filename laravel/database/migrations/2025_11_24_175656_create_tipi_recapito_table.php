<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTipiRecapitoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tipiRecapito', function (Blueprint $table) {
            $table->id('idTipoRecapito');
            $table->string('nome', 50)->unique();
            $table->string('descrizione')->nullable();
            $table->enum('validazione', ['email', 'telefono', 'url', 'string'])->default('string');
            $table->string('formato', 100)->nullable()->comment('Formato di esempio: es. "333 123 4567"');
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
        Schema::dropIfExists('tipiRecapito');
    }
}
