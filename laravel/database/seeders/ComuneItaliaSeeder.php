<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ComuneItaliano;

class ComuneItaliaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Array di comuni rappresentativi da diverse regioni italiane
        $comuni = [
            // Nord
            ['nome' => 'Torino', 'regione' => 'Piemonte', 'provincia' => 'Torino', 'sigla_provincia' => 'TO', 'codice_catastale' => 'L219', 'cap' => '10100'],
            ['nome' => 'Milano', 'regione' => 'Lombardia', 'provincia' => 'Milano', 'sigla_provincia' => 'MI', 'codice_catastale' => 'F205', 'cap' => '20100'],
            ['nome' => 'Venezia', 'regione' => 'Veneto', 'provincia' => 'Venezia', 'sigla_provincia' => 'VE', 'codice_catastale' => 'L736', 'cap' => '30100'],
            ['nome' => 'Bologna', 'regione' => 'Emilia-Romagna', 'provincia' => 'Bologna', 'sigla_provincia' => 'BO', 'codice_catastale' => 'A944', 'cap' => '40100'],
            ['nome' => 'Genova', 'regione' => 'Liguria', 'provincia' => 'Genova', 'sigla_provincia' => 'GE', 'codice_catastale' => 'D969', 'cap' => '16100'],
            
            // Centro  
            ['nome' => 'Firenze', 'regione' => 'Toscana', 'provincia' => 'Firenze', 'sigla_provincia' => 'FI', 'codice_catastale' => 'D612', 'cap' => '50100'],
            ['nome' => 'Roma', 'regione' => 'Lazio', 'provincia' => 'Roma', 'sigla_provincia' => 'RM', 'codice_catastale' => 'H501', 'cap' => '00100'],
            ['nome' => 'Perugia', 'regione' => 'Umbria', 'provincia' => 'Perugia', 'sigla_provincia' => 'PG', 'codice_catastale' => 'G478', 'cap' => '06100'],
            ['nome' => 'Ancona', 'regione' => 'Marche', 'provincia' => 'Ancona', 'sigla_provincia' => 'AN', 'codice_catastale' => 'A271', 'cap' => '60100'],
            
            // Sud
            ['nome' => 'Napoli', 'regione' => 'Campania', 'provincia' => 'Napoli', 'sigla_provincia' => 'NA', 'codice_catastale' => 'F839', 'cap' => '80100'],
            ['nome' => 'Bari', 'regione' => 'Puglia', 'provincia' => 'Bari', 'sigla_provincia' => 'BA', 'codice_catastale' => 'A662', 'cap' => '70100'],
            ['nome' => 'Reggio di Calabria', 'regione' => 'Calabria', 'provincia' => 'Reggio Calabria', 'sigla_provincia' => 'RC', 'codice_catastale' => 'H224', 'cap' => '89100'],
            ['nome' => 'L\'Aquila', 'regione' => 'Abruzzo', 'provincia' => 'L\'Aquila', 'sigla_provincia' => 'AQ', 'codice_catastale' => 'A345', 'cap' => '67100'],
            ['nome' => 'Potenza', 'regione' => 'Basilicata', 'provincia' => 'Potenza', 'sigla_provincia' => 'PZ', 'codice_catastale' => 'G942', 'cap' => '85100'],
            ['nome' => 'Campobasso', 'regione' => 'Molise', 'provincia' => 'Campobasso', 'sigla_provincia' => 'CB', 'codice_catastale' => 'B519', 'cap' => '86100'],
            
            // Isole
            ['nome' => 'Palermo', 'regione' => 'Sicilia', 'provincia' => 'Palermo', 'sigla_provincia' => 'PA', 'codice_catastale' => 'G273', 'cap' => '90100'],
            ['nome' => 'Catania', 'regione' => 'Sicilia', 'provincia' => 'Catania', 'sigla_provincia' => 'CT', 'codice_catastale' => 'C351', 'cap' => '95100'],
            ['nome' => 'Cagliari', 'regione' => 'Sardegna', 'provincia' => 'Cagliari', 'sigla_provincia' => 'CA', 'codice_catastale' => 'B354', 'cap' => '09100'],
            ['nome' => 'Sassari', 'regione' => 'Sardegna', 'provincia' => 'Sassari', 'sigla_provincia' => 'SS', 'codice_catastale' => 'I452', 'cap' => '07100'],
            
            // Regioni autonome
            ['nome' => 'Trento', 'regione' => 'Trentino-Alto Adige/Südtirol', 'provincia' => 'Trento', 'sigla_provincia' => 'TN', 'codice_catastale' => 'L378', 'cap' => '38100'],
        ];

        foreach ($comuni as $comune) {
            ComuneItaliano::create($comune);
        }
    }
}