<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class TipiIndirizzoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $now = Carbon::now();
        
        $tipi = [
            [
                'idTipoIndirizzo' => 1,
                'nome' => 'Residenza',
                'descrizione' => 'Indirizzo di residenza anagrafica',
                'attivo' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'idTipoIndirizzo' => 2,
                'nome' => 'Domicilio',
                'descrizione' => 'Indirizzo di domicilio effettivo',
                'attivo' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'idTipoIndirizzo' => 3,
                'nome' => 'Lavoro',
                'descrizione' => 'Indirizzo del posto di lavoro',
                'attivo' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'idTipoIndirizzo' => 4,
                'nome' => 'Spedizione',
                'descrizione' => 'Indirizzo per spedizioni e consegne',
                'attivo' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'idTipoIndirizzo' => 5,
                'nome' => 'Fatturazione',
                'descrizione' => 'Indirizzo per fatturazione',
                'attivo' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'idTipoIndirizzo' => 6,
                'nome' => 'Sede Legale',
                'descrizione' => 'Sede legale aziendale',
                'attivo' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'idTipoIndirizzo' => 7,
                'nome' => 'Sede Operativa',
                'descrizione' => 'Sede operativa aziendale',
                'attivo' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'idTipoIndirizzo' => 8,
                'nome' => 'Temporaneo',
                'descrizione' => 'Indirizzo temporaneo',
                'attivo' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ];

        DB::table('tipiIndirizzo')->insert($tipi);
        
        $this->command->info('✅ Inseriti ' . count($tipi) . ' tipi di indirizzo');
    }
}