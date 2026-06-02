<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class RenameAndFixComuniItaliaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Prima rimuovo tutte le foreign key e indici (tranne PRIMARY)
        $foreignKeys = collect(DB::select("
            SELECT CONSTRAINT_NAME 
            FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE 
            WHERE TABLE_NAME = 'comuni_italia' 
            AND CONSTRAINT_SCHEMA = DATABASE()
            AND REFERENCED_TABLE_NAME IS NOT NULL
        "))->pluck('CONSTRAINT_NAME');
        
        foreach ($foreignKeys as $fkName) {
            try {
                DB::statement("ALTER TABLE comuni_italia DROP FOREIGN KEY $fkName");
            } catch(Exception $e) {
                // Ignora errori
            }
        }
        
        $indexes = collect(DB::select("SHOW INDEX FROM comuni_italia"))
            ->where('Key_name', '!=', 'PRIMARY')
            ->pluck('Key_name')
            ->unique();
            
        foreach ($indexes as $indexName) {
            try {
                DB::statement("ALTER TABLE comuni_italia DROP INDEX `$indexName`");
            } catch(Exception $e) {
                // Ignora errori
            }
        }
        
        // Rinomino la tabella
        Schema::rename('comuni_italia', 'comuni_italiani');
        
        // Rinomino le colonne per seguire la struttura richiesta
        DB::statement("ALTER TABLE comuni_italiani CHANGE id idComune BIGINT(20) UNSIGNED AUTO_INCREMENT");
        DB::statement("ALTER TABLE comuni_italiani CHANGE nome comune VARCHAR(100) NOT NULL");
        
        // Aggiungo le colonne mancanti con valori di default
        Schema::table('comuni_italiani', function (Blueprint $table) {
            $table->string('zona', 50)->after('provincia')->default('');
            $table->string('codice_istat', 10)->after('sigla_provincia')->default('');
            $table->integer('abitanti')->after('codice_istat')->default(0);
            $table->decimal('superficie', 8, 2)->after('abitanti')->default(0);
            $table->integer('cap_finale')->after('cap')->default(0);
            $table->integer('cap_iniziale')->after('cap_finale')->default(0);
        });
        
        // Rimuovo colonne non necessarie
        Schema::table('comuni_italiani', function (Blueprint $table) {
            $table->dropColumn(['codice_catastale', 'attivo', 'ordinamento', 'deleted_at']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Rollback
        DB::statement("ALTER TABLE comuni_italiani CHANGE created_at created_up TIMESTAMP NULL DEFAULT NULL");
        Schema::rename('comuni_italiani', 'comuni_italia');
    }
}
