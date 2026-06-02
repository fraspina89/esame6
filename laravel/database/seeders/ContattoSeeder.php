<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Contatto;

class ContattoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Creiamo alcuni contatti di esempio con i campi corretti
        Contatto::create([
            'idContattoStato' => 4,
            'nome' => 'Mario',
            'cognome' => 'Rossi',
            'sesso' => 1, // 1 = Maschio
            'codiceFiscale' => 'RSSMRA90E15F205X',
            'partitaIva' => null,
            'cittadinanza' => 'Italiana',
            'idNazioneNascita' => 1,
            'cittaNascita' => 'Roma',
            'provinciaNascita' => 'RM',
            'dataNascita' => '1990-05-15',
            'archiviato' => 0,
            'created_by' => 1,
            'updated_by' => 1
        ]);

        Contatto::create([
            'idContattoStato' => 4,
            'nome' => 'Giulia',
            'cognome' => 'Verdi',
            'sesso' => 2, // 2 = Femmina
            'codiceFiscale' => 'VRDGLI85T43H501Y',
            'partitaIva' => null,
            'cittadinanza' => 'Italiana',
            'idNazioneNascita' => 1,
            'cittaNascita' => 'Milano',
            'provinciaNascita' => 'MI',
            'dataNascita' => '1985-12-03',
            'archiviato' => 0,
            'created_by' => 1,
            'updated_by' => 1
        ]);

        Contatto::create([
            'idContattoStato' => 4,
            'nome' => 'Luca',
            'cognome' => 'Bianchi',
            'sesso' => 1,
            'codiceFiscale' => null,
            'partitaIva' => '12345678901',
            'cittadinanza' => 'Italiana',
            'idNazioneNascita' => 1,
            'cittaNascita' => 'Napoli',
            'provinciaNascita' => 'NA',
            'dataNascita' => '1992-08-22',
            'archiviato' => 0,
            'created_by' => 1,
            'updated_by' => 1
        ]);

        Contatto::create([
            'idContattoStato' => 2, // Stato diverso
            'nome' => 'Anna',
            'cognome' => 'Neri',
            'sesso' => 2,
            'codiceFiscale' => null,
            'partitaIva' => null,
            'cittadinanza' => 'Francese',
            'idNazioneNascita' => 2,
            'cittaNascita' => 'Parigi',
            'provinciaNascita' => null,
            'dataNascita' => '1988-02-14',
            'archiviato' => 1, // Archiviato
            'created_by' => 1,
            'updated_by' => 1
        ]);

        Contatto::create([
            'idContattoStato' => 4,
            'nome' => 'Francesco',
            'cognome' => 'Gialli',
            'sesso' => 1,
            'codiceFiscale' => null,
            'partitaIva' => null,
            'cittadinanza' => 'Italiana',
            'idNazioneNascita' => 1,
            'cittaNascita' => 'Torino',
            'provinciaNascita' => 'TO',
            'dataNascita' => '1995-11-30',
            'archiviato' => 0,
            'created_by' => 1,
            'updated_by' => 1
        ]);
    }
}