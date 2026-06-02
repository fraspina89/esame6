<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateContattiaccessiTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('contattiaccessi', function (Blueprint $table) {
            // 1 - id: BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY
            $table->bigIncrements('id');
            
            // 2 - idContatto: BIGINT(20) UNSIGNED NOT NULL (FK)
            $table->unsignedBigInteger('idContatto');
            
            // 3 - autenticato: TINYINT(1) NULL
            $table->boolean('autenticato')->nullable();
            
            // 4 - ip: VARCHAR(45) NULL
            $table->string('ip', 45)->nullable()->charset('utf8mb4')->collation('utf8mb4_unicode_ci');
            
            // Timestamps
            $table->timestamps();
            
            // Indici
            $table->index('idContatto');
            
            // Foreign Keys
            $table->foreign('idContatto', 'fk_contattiaccessi_contatti')->references('idContatto')->on('contatti')->onDelete('cascade')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('contattiaccessi');
    }
}
