<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddVisualizzatoToCategorieTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Migration disabilitata - la colonna visualizzato non è più necessaria
        // Schema::table('categorie', function (Blueprint $table) {
        //     $table->boolean('visualizzato')->default(1)->after('nome');
        // });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Migration disabilitata - non facciamo rollback
        // Schema::table('categorie', function (Blueprint $table) {
        //     $table->dropColumn('visualizzato');
        // });
    }
}
