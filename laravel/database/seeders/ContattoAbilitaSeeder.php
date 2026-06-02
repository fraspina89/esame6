<?php

namespace Database\Seeders;

use App\Models\ContattoAbilita;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ContattoAbilitaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Disabilita i controlli delle foreign key
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        // Svuota la tabella
        ContattoAbilita::truncate();

        // Abilità essenziali per i ruoli richiesti dall’esame
        $abilita = [
            [
                'nome' => 'Registrazione',
                'sku' => 'REGISTRAZIONE',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nome' => 'Visualizza risorse',
                'sku' => 'VISUALIZZA',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nome' => 'Modifica dati personali',
                'sku' => 'MODIFICA_PROPRI_DATI',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nome' => 'Aggiungi crediti',
                'sku' => 'AGGIUNGI_CREDITI',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nome' => 'Amministrazione',
                'sku' => 'AMMINISTRAZIONE',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        // Inserisci i dati
        ContattoAbilita::insert($abilita);

        // Riabilita i controlli delle foreign key
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $this->command->info('ContattoAbilita seeded successfully!');
    }
}
