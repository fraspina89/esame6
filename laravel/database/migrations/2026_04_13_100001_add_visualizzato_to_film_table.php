<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddVisualizzatoToFilmTable extends Migration
{
    public function up()
    {
        Schema::table('film', function (Blueprint $table) {
            $table->boolean('visualizzato')->default(1)->after('anno');
        });
    }

    public function down()
    {
        Schema::table('film', function (Blueprint $table) {
            $table->dropColumn('visualizzato');
        });
    }
}
