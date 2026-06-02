<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class ChangeDurataColumnInFilmTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // tinyInteger unsigned (max 255) → smallInteger unsigned (max 65535)
        // Using raw SQL to avoid Doctrine DBAL compatibility issues
        DB::statement('ALTER TABLE film MODIFY COLUMN durata SMALLINT UNSIGNED NULL');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement('ALTER TABLE film MODIFY COLUMN durata TINYINT UNSIGNED NULL');
    }
}
