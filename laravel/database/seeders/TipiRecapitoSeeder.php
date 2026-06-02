<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class TipiRecapitoSeeder extends Seeder
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
                'idTipoRecapito' => 1,
                'nome' => 'Email',
                'descrizione' => 'Indirizzo email personale o aziendale',
                'validazione' => 'email',
                'formato' => 'esempio@email.it',
                'attivo' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'idTipoRecapito' => 2,
                'nome' => 'Telefono Fisso',
                'descrizione' => 'Numero di telefono fisso',
                'validazione' => 'telefono',
                'formato' => '011 123456',
                'attivo' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'idTipoRecapito' => 3,
                'nome' => 'Cellulare',
                'descrizione' => 'Numero di telefono cellulare',
                'validazione' => 'telefono',
                'formato' => '333 123 4567',
                'attivo' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'idTipoRecapito' => 4,
                'nome' => 'Fax',
                'descrizione' => 'Numero di fax',
                'validazione' => 'telefono',
                'formato' => '011 123456',
                'attivo' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'idTipoRecapito' => 5,
                'nome' => 'Email PEC',
                'descrizione' => 'Posta Elettronica Certificata',
                'validazione' => 'email',
                'formato' => 'esempio@pec.it',
                'attivo' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'idTipoRecapito' => 6,
                'nome' => 'Skype',
                'descrizione' => 'Nome utente Skype',
                'validazione' => 'string',
                'formato' => 'nome.utente',
                'attivo' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'idTipoRecapito' => 7,
                'nome' => 'WhatsApp',
                'descrizione' => 'Numero WhatsApp',
                'validazione' => 'telefono',
                'formato' => '333 123 4567',
                'attivo' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'idTipoRecapito' => 8,
                'nome' => 'Sito Web',
                'descrizione' => 'Indirizzo sito web',
                'validazione' => 'url',
                'formato' => 'https://www.esempio.it',
                'attivo' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ];

        DB::table('tipiRecapito')->insert($tipi);
        
        $this->command->info('✅ Inseriti ' . count($tipi) . ' tipi di recapito');
    }
}