<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateContattiruoliTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('contattiRuoli', function (Blueprint $table) {
            // 1 - idContattoRuolo: BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY
            $table->bigIncrements('idContattoRuolo');
            
            // 2 - nome: VARCHAR(255) NOT NULL
            $table->string('nome', 255)->charset('utf8mb4')->collation('utf8mb4_unicode_ci');
            
            // Soft deletes
            $table->softDeletes();
            
            // Timestamps
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('contattiRuoli');
    }
}
