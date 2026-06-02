<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Contatto;
use App\Models\ContattoRecapito;
use Illuminate\Support\Facades\DB;

class ContattoRecapitiSeeder extends Seeder
{
    public function run()
    {
        $contatti = Contatto::all();

        if ($contatti->isEmpty()) {
            $this->command->warn('Nessun contatto trovato: esegui prima i seed dei contatti.');
            return;
        }

        // Rimuovo eventuali recapiti esistenti per evitare duplicati
        DB::table('recapiti')->whereIn('idContatto', $contatti->pluck('idContatto')->toArray())->delete();

        $created = 0;
        foreach ($contatti as $i => $contatto) {
            // Email (tipo 1)
            ContattoRecapito::create([
                'idContatto' => $contatto->idContatto,
                'idTipoRecapito' => 1,
                'valore' => strtolower($contatto->nome) . '.' . strtolower($contatto->cognome) . '@example.com',
                'descrizione' => 'Email principale',
                'preferito' => true,
            ]);
            $created++;

            // Cellulare (tipo 3)
            $numero = '3' . rand(30, 39) . rand(1000000, 9999999);
            ContattoRecapito::create([
                'idContatto' => $contatto->idContatto,
                'idTipoRecapito' => 3,
                'valore' => $numero,
                'descrizione' => 'Cellulare personale',
                'preferito' => false,
            ]);
            $created++;
        }

        $this->command->info("Inseriti {$created} recapiti per contatti esistenti.");
    }
}
