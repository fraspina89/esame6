<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up()
    {
        if (! Schema::hasTable('contattiauth')) {
            return;
        }

        $contatti = [
            ['idContatto' => 1, 'user' => 'ospite@example.com'],
            ['idContatto' => 2, 'user' => 'user@example.com'],
            ['idContatto' => 3, 'user' => 'admin@example.com'],
        ];

        foreach ($contatti as $c) {
            $existing = DB::table('contattiauth')->where('idContatto', $c['idContatto'])->first();

            $secret = hash('sha512', trim(Str::random(200)));

            if ($existing) {
                DB::table('contattiauth')->where('idContatto', $c['idContatto'])->update([
                    'user' => $c['user'],
                    'secretJWT' => $secret,
                    'inizioSfida' => now(),
                    'obbligoCambio' => 0,
                    'updated_at' => now(),
                ]);
            } else {
                DB::table('contattiauth')->insert([
                    'idContatto' => $c['idContatto'],
                    'user' => $c['user'],
                    'secretJWT' => $secret,
                    'inizioSfida' => now(),
                    'obbligoCambio' => 0,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }

    public function down()
    {
        // Non rimuoviamo dati al rollback per sicurezza.
    }
};
