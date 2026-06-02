<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class FixContattiContattiruoloKeys extends Migration
{
    public function up()
    {
        if (! Schema::hasTable('contatti_contattiruolo')) {
            return;
        }

        // DROP any existing foreign keys on this table
        $fks = DB::select("SELECT CONSTRAINT_NAME FROM information_schema.KEY_COLUMN_USAGE WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'contatti_contattiruolo' AND REFERENCED_TABLE_NAME IS NOT NULL");
        foreach ($fks as $fk) {
            try {
                DB::statement("ALTER TABLE contatti_contattiruolo DROP FOREIGN KEY {$fk->CONSTRAINT_NAME}");
            } catch (\Exception $e) {
                // ignore
            }
        }

        // DROP indices that might conflict
        $indexes = DB::select("SHOW INDEX FROM contatti_contattiruolo");
        $toDrop = [];
        foreach ($indexes as $idx) {
            $k = $idx->Key_name;
            if ($k !== 'PRIMARY' && $k !== 'contatti_contattiruolo_contatto_ruolo_unique') {
                $toDrop[$k] = true;
            }
        }
        foreach (array_keys($toDrop) as $iname) {
            try { DB::statement("ALTER TABLE contatti_contattiruolo DROP INDEX $iname"); } catch (\Exception $e) { }
        }

        // Add indexes and unique constraint (idempotent via try/catch)
        try {
            Schema::table('contatti_contattiruolo', function (Blueprint $table) {
                $table->index('idContatto', 'contatti_contattiruolo_idcontatto_index');
                $table->index('idContattoRuolo', 'contatti_contattiruolo_idcontattoruolo_index');
                $table->unique(['idContatto', 'idContattoRuolo'], 'contatti_contattiruolo_contatto_ruolo_unique');
            });
        } catch (\Exception $e) {
            // ignore
        }

        // Recreate FKs with stable names
        try {
            Schema::table('contatti_contattiruolo', function (Blueprint $table) {
                $table->foreign('idContatto', 'fk_ccr_idcontatto')
                    ->references('idContatto')->on('contatti')->onDelete('cascade');

                $table->foreign('idContattoRuolo', 'fk_ccr_idcontattoruolo')
                    ->references('idContattoRuolo')->on('contattiRuoli')->onDelete('cascade');
            });
        } catch (\Exception $e) {
            // ignore
        }
    }

    public function down()
    {
        if (! Schema::hasTable('contatti_contattiruolo')) {
            return;
        }

        Schema::table('contatti_contattiruolo', function (Blueprint $table) {
            try { $table->dropForeign('fk_ccr_idcontatto'); } catch (\Exception $e) { }
            try { $table->dropForeign('fk_ccr_idcontattoruolo'); } catch (\Exception $e) { }
            try { $table->dropIndex('contatti_contattiruolo_idcontatto_index'); } catch (\Exception $e) { }
            try { $table->dropIndex('contatti_contattiruolo_idcontattoruolo_index'); } catch (\Exception $e) { }
            try { $table->dropUnique('contatti_contattiruolo_contatto_ruolo_unique'); } catch (\Exception $e) { }
        });
    }
}
