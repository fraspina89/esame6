<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Traduzione;

class TraduzioneSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $traduzioni = [
            ['lingua_id' => 1, 'chiave' => 'app.welcome', 'valore' => 'Benvenuto', 'gruppo' => 'app'],
            ['lingua_id' => 2, 'chiave' => 'app.welcome', 'valore' => 'Welcome', 'gruppo' => 'app'],
            ['lingua_id' => 1, 'chiave' => 'auth.login.success', 'valore' => 'Accesso eseguito con successo', 'gruppo' => 'auth'],
            ['lingua_id' => 2, 'chiave' => 'auth.login.success', 'valore' => 'Login successful', 'gruppo' => 'auth'],
            ['lingua_id' => 1, 'chiave' => 'auth.failed', 'valore' => 'Credenziali non valide', 'gruppo' => 'auth'],
            ['lingua_id' => 2, 'chiave' => 'auth.failed', 'valore' => 'Invalid credentials', 'gruppo' => 'auth']
        ];

        foreach ($traduzioni as $t) {
            Traduzione::updateOrCreate(
                ['lingua_id' => $t['lingua_id'], 'chiave' => $t['chiave']],
                $t
            );
        }
    }
}
