<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateContattiauthTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('contattiAuth', function (Blueprint $table) {
            $table->bigIncrements('idContattoAuth');
            $table->unsignedBigInteger('idContatto');
            $table->string('user', 255)->charset('utf8mb4')->collation('utf8mb4_unicode_ci');
            $table->text('sfida')->charset('utf8mb4')->collation('utf8mb4_unicode_ci')->nullable();
            $table->text('secretJWT')->charset('utf8mb4')->collation('utf8mb4_unicode_ci')->nullable();
            $table->datetime('inizioSfida')->nullable();
            $table->boolean('obbligoCambio')->default(0);
            $table->timestamps();
            $table->softDeletes();
            
            // Indici
            $table->index('idContatto', 'contattiAuth_idContatto_index');
            
            // Foreign key
            $table->foreign('idContatto', 'fk_contattiAuth_contatti')
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
        Schema::dropIfExists('contattiAuth');
    }
}
