<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Use raw statements to avoid requiring doctrine/dbal for column change
        DB::statement("ALTER TABLE `contatti` MODIFY `created_by` bigint(20) unsigned NULL");
        DB::statement("ALTER TABLE `contatti` MODIFY `updated_by` bigint(20) unsigned NULL");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement("ALTER TABLE `contatti` MODIFY `created_by` bigint(20) unsigned NOT NULL");
        DB::statement("ALTER TABLE `contatti` MODIFY `updated_by` bigint(20) unsigned NOT NULL");
    }
};
