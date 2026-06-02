<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateContattiStatiTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('contattistati', function (Blueprint $table) {
            // 1. PRIMARY KEY - idContattoStato BIGINT(20) UNSIGNED AUTO_INCREMENT
            $table->bigIncrements('idContattoStato');
            
            // 2. nome VARCHAR(255) NOT NULL, utf8mb4_unicode_ci
            $table->string('nome', 255)->charset('utf8mb4')->collation('utf8mb4_unicode_ci');
            
            // 3. deleted_at TIMESTAMP NULL (SoftDeletes)
            $table->softDeletes();
            
            // 4-5. created_at, updated_at TIMESTAMP NULL
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
        Schema::dropIfExists('contattistati');
    }
}
