<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Contatto;
use App\Models\ContattoIndirizzo;
use App\Models\ComuneItaliano;
use App\Models\Nazione;
use App\Models\TipoIndirizzo;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class ContattoIndirizziSeeder extends Seeder
{
    public function run()
    {
        $contatti = Contatto::all();

        if ($contatti->isEmpty()) {
            $this->command->warn('Nessun contatto trovato: esegui prima i seed dei contatti.');
            return;
        }

        $italia = Nazione::where('iso', 'IT')->first() ?? Nazione::first();
        $residenzaTipo = TipoIndirizzo::where('nome', 'Residenza')->first();
        $lavoroTipo = TipoIndirizzo::where('nome', 'Lavoro')->first();

        if (!$residenzaTipo || !$lavoroTipo) {
            $this->command->warn('Tipi indirizzo mancanti: esegui TipiIndirizzoSeeder.');
            return;
        }

        // Rimuovo eventuali indirizzi esistenti per questi contatti (evita duplicati)
        DB::table('indirizzi')->whereIn('idContatto', $contatti->pluck('idContatto')->toArray())->delete();

        $created = 0;
        foreach ($contatti as $contatto) {
            // Indirizzo di residenza
            $comune = ComuneItaliano::inRandomOrder()->first();
            if ($comune) {
                ContattoIndirizzo::create([
                    'idContatto' => $contatto->idContatto,
                    'idTipologiaIndirizzo' => $residenzaTipo->idTipoIndirizzo,
                    'idNazione' => $italia ? $italia->idNazione : 1,
                    'cap' => $comune->cap,
                    'comune' => $comune->comune,
                    'indirizzo' => 'Via ' . $this->randomStreetName($comune->comune),
                    'civico' => rand(1, 200),
                    'localita' => null,
                ]);
                $created++;
            }
        }

        $this->command->info("Inseriti {$created} indirizzi di residenza per contatti esistenti.");
    }

    private function randomStreetName($comune)
    {
        $streets = ['Roma', 'Garibaldi', 'Cavour', 'Mazzini', 'Manzoni', 'Firenze', 'De Amicis', 'Unità'];
        return $streets[array_rand($streets)];
    }

    private function randomBusinessStreet()
    {
        $streets = ['Industria', 'Commercio', 'Artigiani', 'Tecnologia', 'Affari'];
        return $streets[array_rand($streets)];
    }
}
