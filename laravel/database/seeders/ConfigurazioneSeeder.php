<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Configurazione;

class ConfigurazioneSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Configurazioni essenziali per l'autenticazione e sicurezza
        $configurazioni = [
            [
                'chiave' => 'maxLoginErrati',
                'valore' => '5'
            ],
            [
                'chiave' => 'durata_sfida',
                'valore' => '30'
            ],
            [
                'chiave' => 'durata_sessione', 
                'valore' => '300'
            ],
            [
                'chiave' => 'storicoPsw',
                'valore' => '3'
            ],
            [
                // Soglia in secondi prima della scadenza della password per mostrare l'avviso
                // Default: 90 giorni = 7776000 secondi
                // Mettere 0 per disabilitare la scadenza password
                'chiave' => 'soglia_scadenza_password',
                'valore' => '7776000'
            ],
            [
                'chiave' => 'nome_applicazione',
                'valore' => 'CodeX Server'
            ],
            [
                'chiave' => 'versione_api',
                'valore' => '1.0'
            ],
            [
                'chiave' => 'ambiente',
                'valore' => 'development'
            ],
            [
                'chiave' => 'debug_mode',
                'valore' => 'true'
            ]
        ];

        // Creiamo le configurazioni se non esistono già
        foreach ($configurazioni as $config) {
            Configurazione::firstOrCreate(
                ['chiave' => $config['chiave']],
                ['valore' => $config['valore']]
            );
        }
    }
}
