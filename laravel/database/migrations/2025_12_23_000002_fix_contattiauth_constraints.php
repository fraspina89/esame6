<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Database\Schema\Blueprint;

return new class extends Migration
{
    public function up()
    {
        if (! Schema::hasTable('contattiauth')) {
            return;
        }

        // 1) Popola secretJWT solo dove mancante
        $rows = DB::table('contattiauth')->whereNull('secretJWT')->get();
        foreach ($rows as $r) {
            DB::table('contattiauth')->where('idContattoAuth', $r->idContattoAuth)->update([
                'secretJWT' => hash('sha512', trim(Str::random(200))),
                'updated_at' => now(),
            ]);
        }

        // 2) Aggiunge indice unico su `user` (silenzia errori se già presente)
        try {
            Schema::table('contattiauth', function (Blueprint $table) {
                $table->unique('user');
            });
        } catch (\Throwable $e) {
            // Ignora errori (indice già presente o altri problemi)
        }

        // 3) Prova ad aggiungere FK su idContatto -> contatti(idContatto)
        // Non modifica tipi di colonna per evitare dipendenze (doctrine/dbal)
        try {
            Schema::table('contattiauth', function (Blueprint $table) {
                $table->foreign('idContatto')->references('idContatto')->on('contatti')->onDelete('cascade');
            });
        } catch (\Throwable $e) {
            // Ignora se non è possibile aggiungere la FK
        }
    }

    public function down()
    {
        // Non rimuoviamo dati/indici automaticamente al rollback per sicurezza.
    }
};
