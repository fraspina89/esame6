<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ContattoStato;

class ContattoStatoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Pulire la tabella prima di inserire (in caso di re-run)
        ContattoStato::truncate();

        // Stati logici del sistema contatti
        $stati = [
            [
                'idContattoStato' => 1,
                'nome' => 'Attivo',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'idContattoStato' => 2, 
                'nome' => 'Sospeso',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'idContattoStato' => 3,
                'nome' => 'Eliminato', 
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'idContattoStato' => 4,
                'nome' => 'Disabilitato', // Default per nuovi contatti
                'created_at' => now(),
                'updated_at' => now()
            ]
        ];

        // Inserimento degli stati
        foreach ($stati as $stato) {
            ContattoStato::create($stato);
        }

        $this->command->info('✅ Creati ' . count($stati) . ' stati per i contatti');
        $this->command->info('🎯 Stato default (4): Disabilitato');
    }
}
