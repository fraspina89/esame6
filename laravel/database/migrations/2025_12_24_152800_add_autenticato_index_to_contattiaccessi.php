<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddAutenticatoIndexToContattiaccessi extends Migration
{
    public function up()
    {
        Schema::table('contattiaccessi', function (Blueprint $table) {
            $table->index('autenticato', 'idx_contattiaccessi_autenticato');
        });
    }

    public function down()
    {
        Schema::table('contattiaccessi', function (Blueprint $table) {
            $table->dropIndex('idx_contattiaccessi_autenticato');
        });
    }
}