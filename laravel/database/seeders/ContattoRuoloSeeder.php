<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ContattoRuolo;

class ContattoRuoloSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Ruoli base del sistema (come dal professore)
        $ruoli = [
            ['nome' => 'Amministratore'],
            ['nome' => 'Utente'], 
            ['nome' => 'Ospite']
        ];

        // Creiamo i ruoli se non esistono già
        foreach ($ruoli as $ruolo) {
            ContattoRuolo::firstOrCreate(
                ['nome' => $ruolo['nome']]
            );
        }
    }
}
