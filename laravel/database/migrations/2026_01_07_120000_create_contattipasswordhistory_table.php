<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateContattipasswordhistoryTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('contattiPasswordHistory', function (Blueprint $table) {
            $table->bigIncrements('idContattoPasswordHistory');
            $table->unsignedBigInteger('idContatto');
            $table->string('psw', 255)->charset('utf8mb4')->collation('utf8mb4_unicode_ci');
            $table->string('sale', 255)->nullable()->charset('utf8mb4')->collation('utf8mb4_unicode_ci');
            $table->timestamps();

            $table->index('idContatto');
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
        Schema::dropIfExists('contattiPasswordHistory');
    }
}
