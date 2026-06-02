<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateContattiSessioniTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('contattiSessioni', function (Blueprint $table) {
            // 1 - idContattoSessione: BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY
            $table->bigIncrements('idContattoSessione');
            
            // 2 - idContatto: BIGINT(20) UNSIGNED NOT NULL (chiave verde + FK)
            $table->unsignedBigInteger('idContatto')->nullable(false);
            
            // 3 - token: TEXT NULL
            $table->text('token')->nullable();
            
            // 4 - inizioSessione: BIGINT(20) NULL
            $table->bigInteger('inizioSessione')->nullable();
            
            // Timestamps
            $table->timestamps();
            
            // Indici
            $table->index('idContatto', 'idx_contattiSessioni_idContatto');
            
            // Foreign Keys
            $table->foreign('idContatto', 'fk_contattiSessioni_contatti')
                  ->references('idContatto')
                  ->on('contatti')
                  ->onDelete('cascade')
                  ->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('contattiSessioni');
    }
}
