<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateContattiTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('contatti', function (Blueprint $table) {
            // 1 - idContatto: BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY (gialla)
            $table->bigIncrements('idContatto');
            
            // 2 - idContattoStato: BIGINT(20) UNSIGNED NOT NULL DEFAULT '4' INDEX (verde)
            $table->unsignedBigInteger('idContattoStato', false)->default('4');
            
            // 3 - nome: VARCHAR(45) NULL DEFAULT NULL
            $table->string('nome', 45)->nullable()->default(null)->charset('utf8mb4')->collation('utf8mb4_unicode_ci');
            
            // 4 - cognome: VARCHAR(45) NOT NULL
            $table->string('cognome', 45)->charset('utf8mb4')->collation('utf8mb4_unicode_ci');
            
            // 5 - sesso: TINYINT(3) UNSIGNED NULL DEFAULT NULL
            $table->unsignedTinyInteger('sesso')->nullable()->default(null);
            
            // 6 - codiceFiscale: VARCHAR(20) NULL DEFAULT NULL
            $table->string('codiceFiscale', 20)->nullable()->default(null)->charset('utf8mb4')->collation('utf8mb4_unicode_ci');
            
            // 7 - partitaIva: VARCHAR(20) NULL DEFAULT NULL
            $table->string('partitaIva', 20)->nullable()->default(null)->charset('utf8mb4')->collation('utf8mb4_unicode_ci');
            
            // 8 - cittadinanza: VARCHAR(45) NULL DEFAULT NULL
            $table->string('cittadinanza', 45)->nullable()->default(null)->charset('utf8mb4')->collation('utf8mb4_unicode_ci');
            
            // 9 - idNazioneNascita: BIGINT(20) UNSIGNED NULL DEFAULT NULL INDEX (verde)
            $table->unsignedBigInteger('idNazioneNascita')->nullable()->default(null);
            
            // 10 - cittaNascita: VARCHAR(45) NULL DEFAULT NULL
            $table->string('cittaNascita', 45)->nullable()->default(null)->charset('utf8mb4')->collation('utf8mb4_unicode_ci');
            
            // 11 - provinciaNascita: VARCHAR(45) NULL DEFAULT NULL
            $table->string('provinciaNascita', 45)->nullable()->default(null)->charset('utf8mb4')->collation('utf8mb4_unicode_ci');
            
            // 12 - dataNascita: DATE NULL DEFAULT NULL
            $table->date('dataNascita')->nullable()->default(null);
            
            // 13 - deleted_at: TIMESTAMP NULL DEFAULT NULL
            $table->timestamp('deleted_at')->nullable()->default(null);
            
            // 14 - archiviato: TINYINT(3) UNSIGNED NOT NULL DEFAULT '0' INDEX (verde)
            $table->unsignedTinyInteger('archiviato')->default('0');
            
            // 15 - created_by: BIGINT(20) UNSIGNED NOT NULL INDEX (verde)
            $table->unsignedBigInteger('created_by');
            
            // 16 - updated_by: BIGINT(20) UNSIGNED NOT NULL INDEX (verde)
            $table->unsignedBigInteger('updated_by');
            
            // 17 - created_at: TIMESTAMP NULL DEFAULT NULL
            $table->timestamp('created_at')->nullable()->default(null);
            
            // 18 - updated_at: TIMESTAMP NULL DEFAULT NULL
            $table->timestamp('updated_at')->nullable()->default(null);
            
            // Indici verdi specificati
            $table->index('idContattoStato'); // 2 verde
            $table->index('idNazioneNascita'); // 9 verde  
            $table->index('archiviato'); // 14 verde
            $table->index('created_by'); // 15 verde
            $table->index('updated_by'); // 16 verde
            
            // Foreign Keys (condivisioni)
            $table->foreign('idContattoStato', 'fk_contatti_stato')
                  ->references('idContattoStato')->on('contattistati')
                  ->onDelete('restrict')->onUpdate('cascade');
            
            $table->foreign('idNazioneNascita', 'fk_contatti_nazione_nascita')
                  ->references('idNazione')->on('nazioni')
                  ->onDelete('restrict')->onUpdate('cascade');
            
            $table->foreign('created_by', 'fk_contatti_created_by')
                  ->references('idContatto')->on('contatti')
                  ->onDelete('restrict')->onUpdate('cascade');
            
            $table->foreign('updated_by', 'fk_contatti_updated_by')
                  ->references('idContatto')->on('contatti')
                  ->onDelete('restrict')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('contatti');
    }
}
