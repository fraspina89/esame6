<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // \App\Models\User::factory(10)->create();
        
        $this->call([
            CategoriaSeeder::class,
            ContattoSeeder::class,
            ConfigurazioneSeeder::class,
            ContattoRuoloSeeder::class,
            ContattoStatoSeeder::class,
            ContattoAbilitaSeeder::class,
            NazioneSeeder::class,
            TipiRecapitoSeeder::class,
            TipiIndirizzoSeeder::class,
            SerieTvSeeder::class,   // deve venire prima di EpisodioSeeder (FK idSerie)
            EpisodioSeeder::class,
        ]);
    }
}
