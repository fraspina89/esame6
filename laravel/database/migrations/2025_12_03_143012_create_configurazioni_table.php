<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateConfigurazioniTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('configurazioni', function (Blueprint $table) {
            // 1 - idConfigurazione: BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY
            $table->bigIncrements('idConfigurazione');
            
            // 2 - chiave: VARCHAR(255) NOT NULL
            $table->string('chiave', 255)->charset('utf8mb4')->collation('utf8mb4_unicode_ci');
            
            // 3 - valore: TEXT NULL
            $table->text('valore')->nullable();
            
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
        Schema::dropIfExists('configurazioni');
    }
}
