<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Lingua;

class LinguaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $lingue = [
            [
                'codice' => 'it',
                'nome' => 'Italiano',
                'nome_nativo' => 'Italiano',
                'bandiera' => '🇮🇹',
                'attivo' => true,
                'predefinita' => true,
                'ordinamento' => 1
            ],
            [
                'codice' => 'en',
                'nome' => 'English',
                'nome_nativo' => 'English',
                'bandiera' => '🇬🇧',
                'attivo' => true,
                'predefinita' => false,
                'ordinamento' => 2
            ],
            [
                'codice' => 'fr',
                'nome' => 'Français',
                'nome_nativo' => 'Français',
                'bandiera' => '🇫🇷',
                'attivo' => true,
                'predefinita' => false,
                'ordinamento' => 3
            ],
            [
                'codice' => 'es',
                'nome' => 'Español',
                'nome_nativo' => 'Español',
                'bandiera' => '🇪🇸',
                'attivo' => true,
                'predefinita' => false,
                'ordinamento' => 4
            ],
            [
                'codice' => 'de',
                'nome' => 'Deutsch',
                'nome_nativo' => 'Deutsch',
                'bandiera' => '🇩🇪',
                'attivo' => true,
                'predefinita' => false,
                'ordinamento' => 5
            ]
        ];

        foreach ($lingue as $lingua) {
            Lingua::create($lingua);
        }
    }
}
