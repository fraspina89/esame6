<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class FixCategorieTableStructure extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('categorie', function (Blueprint $table) {
            // Rimuove la colonna visualizzato se esiste
            if (Schema::hasColumn('categorie', 'visualizzato')) {
                $table->dropColumn('visualizzato');
            }
        });
        
        // NON rimuovere più AUTO_INCREMENT da idCategoria
        // DB::statement('ALTER TABLE categorie MODIFY idCategoria TINYINT UNSIGNED NOT NULL');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('categorie', function (Blueprint $table) {
            // Riaggiunge la colonna visualizzato se necessiamo fare rollback
            $table->boolean('visualizzato')->default(1)->after('nome');
        });
        
        // Rimette AUTO_INCREMENT se facciamo rollback
        DB::statement('ALTER TABLE categorie MODIFY idCategoria TINYINT UNSIGNED AUTO_INCREMENT');
    }
}
