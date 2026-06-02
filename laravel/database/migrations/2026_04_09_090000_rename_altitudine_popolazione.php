<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RenameAltitudinePopolazione extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasTable('comuni_italiani')) {
            Schema::table('comuni_italiani', function (Blueprint $table) {
                // rename only if existing
                if (Schema::hasColumn('comuni_italiani', 'altitudine')) {
                    $table->renameColumn('altitudine', 'cap_finale');
                }
                if (Schema::hasColumn('comuni_italiani', 'popolazione_residente')) {
                    $table->renameColumn('popolazione_residente', 'cap_iniziale');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (Schema::hasTable('comuni_italiani')) {
            Schema::table('comuni_italiani', function (Blueprint $table) {
                if (Schema::hasColumn('comuni_italiani', 'cap_finale')) {
                    $table->renameColumn('cap_finale', 'altitudine');
                }
                if (Schema::hasColumn('comuni_italiani', 'cap_iniziale')) {
                    $table->renameColumn('cap_iniziale', 'popolazione_residente');
                }
            });
        }
    }
}
