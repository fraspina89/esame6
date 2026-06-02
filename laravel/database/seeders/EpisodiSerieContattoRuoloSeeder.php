<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class EpisodiSerieContattoRuoloSeeder extends Seeder
{
    public function run()
    {
        // ensure roles/ability exist
        $idG = DB::table('contattiRuoli')->where('nome', 'Gialla')->value('idContattoRuolo');
        $idV = DB::table('contattiRuoli')->where('nome', 'Verde')->value('idContattoRuolo');
        $idCon = DB::table('contattiabilita')->where('sku', 'CONDIVISIONE')->value('idContattoAbilita');

        if (! $idG) {
            $idG = DB::table('contattiRuoli')->insertGetId(['nome' => 'Gialla', 'created_at' => now(), 'updated_at' => now()]);
        }
        if (! $idV) {
            $idV = DB::table('contattiRuoli')->insertGetId(['nome' => 'Verde', 'created_at' => now(), 'updated_at' => now()]);
        }
        if (! $idCon) {
            $idCon = DB::table('contattiabilita')->insertGetId(['nome' => 'Condivisione', 'sku' => 'CONDIVISIONE', 'created_at' => now(), 'updated_at' => now()]);
        }

        // episodio 1 -> Gialla
        if (DB::getSchemaBuilder()->hasTable('episodi_contattiruolo')) {
            DB::table('episodi_contattiruolo')->where('idEpisodio', 1)->delete();
            DB::table('episodi_contattiruolo')->insert(['idEpisodio' => 1, 'idContattoRuolo' => $idG, 'created_at' => now(), 'updated_at' => now()]);
            $this->command->info('Episodio 1 assegnato ruolo Gialla');
        } else {
            $this->command->error('Tabella episodi_contattiruolo non trovata');
        }

        // serie di episodio 2 -> Verde + CONDIVISIONE
        if (DB::getSchemaBuilder()->hasTable('episodi')) {
            $serie = DB::table('episodi')->where('idEpisodio', 2)->value('idSerie');
            if ($serie) {
                if (DB::getSchemaBuilder()->hasTable('serie_contattiruolo')) {
                    DB::table('serie_contattiruolo')->where('idSerie', $serie)->delete();
                    DB::table('serie_contattiruolo')->insert(['idSerie' => $serie, 'idContattoRuolo' => $idV, 'created_at' => now(), 'updated_at' => now()]);
                    $this->command->info("Serie (id: $serie) assegnata ruolo Verde");
                } else {
                    $this->command->error('Tabella serie_contattiruolo non trovata');
                }

                // assegna abilita CONDIVISIONE al ruolo Verde
                if (DB::getSchemaBuilder()->hasTable('contattiruoli_contattiabilita')) {
                    $exists = DB::table('contattiruoli_contattiabilita')->where('idContattoRuolo', $idV)->where('idContattoAbilita', $idCon)->exists();
                    if (! $exists) {
                        DB::table('contattiruoli_contattiabilita')->insert(['idContattoRuolo' => $idV, 'idContattoAbilita' => $idCon, 'created_at' => now(), 'updated_at' => now()]);
                        $this->command->info('Abilità CONDIVISIONE associata a Verde');
                    }
                }
            } else {
                $this->command->error('Non ho trovato la serie per episodio 2');
            }
        }
    }
}
