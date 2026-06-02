<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ContattiContattiruoloFixSeeder extends Seeder
{
    public function run()
    {
        // Ensure roles exist
        $idG = DB::table('contattiRuoli')->where('nome', 'Gialla')->value('idContattoRuolo');
        if (! $idG) {
            $idG = DB::table('contattiRuoli')->insertGetId([
                'nome' => 'Gialla', 'created_at' => now(), 'updated_at' => now()
            ]);
            $this->command->info("Ruolo 'Gialla' creato (id: $idG)");
        }

        $idV = DB::table('contattiRuoli')->where('nome', 'Verde')->value('idContattoRuolo');
        if (! $idV) {
            $idV = DB::table('contattiRuoli')->insertGetId([
                'nome' => 'Verde', 'created_at' => now(), 'updated_at' => now()
            ]);
            $this->command->info("Ruolo 'Verde' creato (id: $idV)");
        }

        // Ensure ability exists
        $idCon = DB::table('contattiabilita')->where('sku', 'CONDIVISIONE')->value('idContattoAbilita');
        if (! $idCon) {
            $idCon = DB::table('contattiabilita')->insertGetId([
                'nome' => 'Condivisione', 'sku' => 'CONDIVISIONE', 'created_at' => now(), 'updated_at' => now()
            ]);
            $this->command->info("Abilità 'Condivisione' creata (id: $idCon)");
        }

        // Backup existing pivot rows for contatti 1,2,3
        if (DB::getSchemaBuilder()->hasTable('contatti_contattiruolo')) {
            try {
                DB::statement('CREATE TABLE IF NOT EXISTS contatti_contattiruolo_backup LIKE contatti_contattiruolo');
            } catch (\Exception $e) { }

            // cleanup previous backup rows for these contatti (if any)
            try {
                DB::table('contatti_contattiruolo_backup')->whereIn('idContatto', [1,2,3])->delete();
            } catch (\Exception $e) { }

            DB::table('contatti_contattiruolo_backup')->insertUsing(['idContatto','idContattoRuolo','created_at','updated_at'],
                DB::table('contatti_contattiruolo')->select('idContatto','idContattoRuolo','created_at','updated_at')->whereIn('idContatto',[1,2,3])
            );

            // Delete and re-insert desired mapping
            DB::table('contatti_contattiruolo')->whereIn('idContatto',[1,2,3])->delete();

            DB::table('contatti_contattiruolo')->insert([
                ['idContatto' => 1, 'idContattoRuolo' => $idG, 'created_at' => now(), 'updated_at' => now()],
                ['idContatto' => 2, 'idContattoRuolo' => $idV, 'created_at' => now(), 'updated_at' => now()],
                ['idContatto' => 3, 'idContattoRuolo' => $idV, 'created_at' => now(), 'updated_at' => now()],
            ]);

            $this->command->info('Pivot contatti_contattiruolo aggiornato per contatti 1,2,3');
        } else {
            $this->command->error('Tabella contatti_contattiruolo non trovata, skip aggiornamento pivot');
        }

        // Associate CONDIVISIONE to Verde role
        if (DB::getSchemaBuilder()->hasTable('contattiruoli_contattiabilita')) {
            $exists = DB::table('contattiruoli_contattiabilita')
                ->where('idContattoRuolo', $idV)
                ->where('idContattoAbilita', $idCon)
                ->exists();
            if (! $exists) {
                DB::table('contattiruoli_contattiabilita')->insert([
                    'idContattoRuolo' => $idV,
                    'idContattoAbilita' => $idCon,
                    'created_at' => now(), 'updated_at' => now()
                ]);
                $this->command->info("Abilità 'CONDIVISIONE' associata al ruolo 'Verde'");
            } else {
                $this->command->info("Associazione 'Verde'->'CONDIVISIONE' già presente");
            }
        } else {
            $this->command->error('Tabella contattiruoli_contattiabilita non trovata, skip associazione abilità');
        }
    }
}
