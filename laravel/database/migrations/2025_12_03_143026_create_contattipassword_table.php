<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateContattipasswordTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('contattiPassword', function (Blueprint $table) {
            // 1 - idContattoPassword: BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY
            $table->bigIncrements('idContattoPassword');
            
            // 2 - idContatto: BIGINT(20) UNSIGNED NOT NULL (FK)
            $table->unsignedBigInteger('idContatto');
            
            // 3 - psw: VARCHAR(255) NOT NULL
            $table->string('psw', 255)->charset('utf8mb4')->collation('utf8mb4_unicode_ci');
            
            // 4 - sale: VARCHAR(255) NULL
            $table->string('sale', 255)->nullable()->charset('utf8mb4')->collation('utf8mb4_unicode_ci');
            
            // Soft deletes
            $table->softDeletes();
            
            // Timestamps
            $table->timestamps();
            
            // Indici
            $table->index('idContatto');
            
            // Foreign Keys
            $table->foreign('idContatto')->references('idContatto')->on('contatti')->onDelete('cascade')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('contattiPassword');
    }
}
