<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Contatto;
use App\Models\ContattoPassword;
use App\Models\ContattoRuolo;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ContattiSeeder extends Seeder
{
    public function run()
    {
        // Ruoli: Ospite, Utente, Amministratore
        $ruoli = [
            'Ospite' => ContattoRuolo::where('nome', 'Ospite')->first(),
            'Utente' => ContattoRuolo::where('nome', 'Utente')->first(),
            'Amministratore' => ContattoRuolo::where('nome', 'Amministratore')->first(),
        ];

        // Crea 3 contatti di esempio
        $contatti = [
            [
                'nome' => 'Mario',
                'cognome' => 'Rossi',
                'sesso' => 1,
                'codiceFiscale' => 'RSSMRA90E15F205X',
                'partitaIva' => null,
                'cittadinanza' => 'Italiana',
                'idNazioneNascita' => 1,
                'cittaNascita' => 'Roma',
                'provinciaNascita' => 'RM',
                'dataNascita' => '1990-05-15',
                'archiviato' => 0,
                'idContattoStato' => 4,
                'created_by' => 1,
                'updated_by' => 1,
            ],
            [
                'nome' => 'Luca',
                'cognome' => 'Bianchi',
                'sesso' => 1,
                'codiceFiscale' => 'BNCLCU85T43H501Y',
                'partitaIva' => null,
                'cittadinanza' => 'Italiana',
                'idNazioneNascita' => 1,
                'cittaNascita' => 'Milano',
                'provinciaNascita' => 'MI',
                'dataNascita' => '1985-12-03',
                'archiviato' => 0,
                'idContattoStato' => 4,
                'created_by' => 1,
                'updated_by' => 1,
            ],
            [
                'nome' => 'Anna',
                'cognome' => 'Verdi',
                'sesso' => 2,
                'codiceFiscale' => 'VRDANN80A01F205Z',
                'partitaIva' => null,
                'cittadinanza' => 'Italiana',
                'idNazioneNascita' => 1,
                'cittaNascita' => 'Napoli',
                'provinciaNascita' => 'NA',
                'dataNascita' => '1980-01-01',
                'archiviato' => 0,
                'idContattoStato' => 4,
                'created_by' => 1,
                'updated_by' => 1,
            ],
        ];

        foreach ($contatti as $i => $dati) {
            $contatto = Contatto::create($dati);

            // Password hashata
            ContattoPassword::create([
                'idContatto' => $contatto->idContatto,
                'psw' => Hash::make('password123'),
            ]);

            // Associazione ruolo
            if ($i === 0) {
                $contatto->ruoli()->attach($ruoli['Ospite']->idContattoRuolo);
            } elseif ($i === 1) {
                $contatto->ruoli()->attach($ruoli['Utente']->idContattoRuolo);
            } else {
                $contatto->ruoli()->attach($ruoli['Amministratore']->idContattoRuolo);
            }
        }
    }
}
