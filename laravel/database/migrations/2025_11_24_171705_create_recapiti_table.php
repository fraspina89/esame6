<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRecapitiTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('recapiti', function (Blueprint $table) {
            // 1. idRecapito - BIGINT(20) UNSIGNED PRIMARY KEY AUTO_INCREMENT
            $table->bigIncrements('idRecapito');
            
            // 2. idContatto - BIGINT(20) UNSIGNED NOT NULL (Foreign Key)
            $table->unsignedBigInteger('idContatto');
            
            // 3. idTipoRecapito - BIGINT(20) UNSIGNED NOT NULL (Foreign Key)
            $table->unsignedBigInteger('idTipoRecapito');
            
            // 4. valore - VARCHAR(255) NOT NULL (numero telefono, email, etc.)
            $table->string('valore', 255)->nullable(false);
            
            // 5. descrizione - VARCHAR(255) NULL (es: "Telefono casa", "Email lavoro")
            $table->string('descrizione', 255)->nullable();
            
            // 6. preferito - BOOLEAN DEFAULT FALSE
            $table->boolean('preferito')->default(false);
            
            // 7-9. SoftDeletes + Timestamps
            $table->softDeletes(); // deleted_at
            $table->timestamps();   // created_at, updated_at
            
            // Foreign Key Constraints
            $table->foreign('idContatto', 'fk_recapiti_contatto')
                  ->references('idContatto')->on('contatti')
                  ->onDelete('cascade');
                  
            // Nota: idTipoRecapito FK verrà aggiunta dopo aver creato la tabella tipirecapiti
            
            // Indici per performance
            $table->index('idContatto', 'idx_recapiti_contatto');
            $table->index('idTipoRecapito', 'idx_recapiti_tipo');
            $table->index('valore', 'idx_recapiti_valore');
            $table->index('preferito', 'idx_recapiti_preferito');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('recapiti');
    }
}
