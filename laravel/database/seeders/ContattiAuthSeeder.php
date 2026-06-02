<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class ContattiAuthSeeder extends Seeder
{
    public function run()
    {
        // Dati di esempio per i 3 contatti
        $contatti = [
            [
                'idContatto' => 1,
                'user' => 'ospite@example.com',
                'password' => 'password123',
            ],
            [
                'idContatto' => 2,
                'user' => 'utente@example.com',
                'password' => 'password123',
            ],
            [
                'idContatto' => 3,
                'user' => 'admin@example.com',
                'password' => 'password123',
            ],
        ];

        foreach ($contatti as $c) {
            DB::table('contattiauth')->insert([
                'idContatto' => $c['idContatto'],
                'user' => $c['user'],
                'secretJWT' => hash('sha512', trim(Str::random(200))),
                'inizioSfida' => now(),
                'obbligoCambio' => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            DB::table('contattipassword')->insert([
                'idContatto' => $c['idContatto'],
                // store plain password for the legacy auth flow which combines 'psw' and 'sale'
                'psw' => $c['password'],
                'sale' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}