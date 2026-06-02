<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateIndirizziTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('indirizzi', function (Blueprint $table) {
            // 1. idIndirizzo - BIGINT(20) UNSIGNED PRIMARY KEY AUTO_INCREMENT
            $table->bigIncrements('idIndirizzo');
            
            // 2. idContatto - BIGINT(20) UNSIGNED NOT NULL (Foreign Key)
            $table->unsignedBigInteger('idContatto');
            
            // 3. idTipologiaIndirizzo - BIGINT(20) UNSIGNED NOT NULL (Foreign Key)
            $table->unsignedBigInteger('idTipologiaIndirizzo');
            
            // 4. idNazione - BIGINT(20) UNSIGNED NOT NULL (Foreign Key)
            $table->unsignedBigInteger('idNazione');
            
            // 5. cap - VARCHAR(15) NULL
            $table->string('cap', 15)->nullable();
            
            // 6. comune - VARCHAR(255) NOT NULL
            $table->string('comune', 255)->nullable(false);
            
            // 7. indirizzo - VARCHAR(255) NOT NULL
            $table->string('indirizzo', 255)->nullable(false);
            
            // 8. civico - VARCHAR(15) NULL
            $table->string('civico', 15)->nullable();
            
            // 9. localita - VARCHAR(255) NULL
            $table->string('localita', 255)->nullable();
            
            // 10-12. SoftDeletes + Timestamps
            $table->softDeletes(); // deleted_at
            $table->timestamps();   // created_at, updated_at
            
            // Foreign Key Constraints
            $table->foreign('idContatto', 'fk_indirizzi_contatto')
                  ->references('idContatto')->on('contatti')
                  ->onDelete('cascade');
                  
            $table->foreign('idNazione', 'fk_indirizzi_nazione')
                  ->references('idNazione')->on('nazioni')
                  ->onDelete('restrict');
                  
            // Nota: idTipologiaIndirizzo FK verrà aggiunta dopo aver creato la tabella tipiindirizzi
            
            // Indici per performance
            $table->index('idContatto', 'idx_indirizzi_contatto');
            $table->index('idNazione', 'idx_indirizzi_nazione');
            $table->index(['comune', 'cap'], 'idx_indirizzi_comune_cap');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('indirizzi');
    }
}
