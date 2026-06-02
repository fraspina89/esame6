<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RemoveContattiIndexes extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('contatti', function (Blueprint $table) {
            // Rimuove gli indici specificati (chiavi verdi)
            $table->dropIndex(['idContattoStato']); // 2
            $table->dropIndex(['idNazioneNascita']); // 9  
            $table->dropIndex(['archiviato']); // 14
            $table->dropIndex(['created_by']); // 15
            $table->dropIndex(['updated_by']); // 16
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('contatti', function (Blueprint $table) {
            // Ripristina gli indici se necessario
            $table->index('idContattoStato');
            $table->index('idNazioneNascita'); 
            $table->index('archiviato');
            $table->index('created_by');
            $table->index('updated_by');
        });
    }
}
