<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class MakeIdFilmAutoincrement extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Modify the column to be NOT NULL AUTO_INCREMENT
        DB::statement('ALTER TABLE `film` MODIFY `idFilm` BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT;');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Revert to previous definition (non auto-increment)
        DB::statement('ALTER TABLE `film` MODIFY `idFilm` BIGINT(20) UNSIGNED NOT NULL;');
    }
}
