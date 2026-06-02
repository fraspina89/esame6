<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSoftDeleteToSerieTvTable extends Migration
{
    public function up()
    {
        if (!Schema::hasColumn('serie_tv', 'deleted_at')) {
            Schema::table('serie_tv', function (Blueprint $table) {
                $table->softDeletes()->after('annoFine');
            });
        }
    }

    public function down()
    {
        Schema::table('serie_tv', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
    }
}
