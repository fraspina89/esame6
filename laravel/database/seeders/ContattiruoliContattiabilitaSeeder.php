<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ContattiruoliContattiabilitaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Recupero gli ID reali dei ruoli e delle abilità
        $ruoli = DB::table('contattiruoli')->pluck('idContattoRuolo', 'nome');
        $abilita = DB::table('contattiabilita')->pluck('idContattoAbilita', 'sku');

        // Associazioni corrette secondo la logica d'esame
        $associazioni = [
            // Ospite: solo REGISTRAZIONE
            ['ruolo' => 'Ospite', 'abilita' => 'REGISTRAZIONE'],

            // Utente: VISUALIZZA, MODIFICA_PROPRI_DATI, AGGIUNGI_CREDITI
            ['ruolo' => 'Utente', 'abilita' => 'VISUALIZZA'],
            ['ruolo' => 'Utente', 'abilita' => 'MODIFICA_PROPRI_DATI'],
            ['ruolo' => 'Utente', 'abilita' => 'AGGIUNGI_CREDITI'],

            // Amministratore: tutte le abilità
            ['ruolo' => 'Amministratore', 'abilita' => 'REGISTRAZIONE'],
            ['ruolo' => 'Amministratore', 'abilita' => 'VISUALIZZA'],
            ['ruolo' => 'Amministratore', 'abilita' => 'MODIFICA_PROPRI_DATI'],
            ['ruolo' => 'Amministratore', 'abilita' => 'AGGIUNGI_CREDITI'],
            ['ruolo' => 'Amministratore', 'abilita' => 'AMMINISTRAZIONE'],
        ];

        // Inserisco le associazioni solo se la tabella pivot è vuota
        if (DB::table('contattiruoli_contattiabilita')->count() == 0) {
            foreach ($associazioni as $assoc) {
                $idRuolo = $ruoli->get($assoc['ruolo']);
                $idAbilita = $abilita->get($assoc['abilita']);
                if ($idRuolo && $idAbilita) {
                    DB::table('contattiruoli_contattiabilita')->insert([
                        'idContattoRuolo' => $idRuolo,
                        'idContattoAbilita' => $idAbilita,
                        'created_at' => now(),
                        'updated_at' => now()
                    ]);
                }
            }
            $this->command->info('✅ Associazioni ruoli-abilità inserite secondo la logica d’esame');
        } else {
            $this->command->info('⚠️  La tabella contiene già dei dati, skip inserimento');
        }
    }
}
