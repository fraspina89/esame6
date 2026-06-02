<?php

namespace Database\Seeders;

use App\Models\Nazione;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class NazioneSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Disabilita i controlli delle foreign key
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        
        // Svuota la tabella
        Nazione::truncate();
        
        // Path del file CSV
        $csvFile = database_path('seeders/data/nazioni.csv');
        
        if (!file_exists($csvFile)) {
            $this->command->error("File CSV non trovato: {$csvFile}");
            return;
        }
        
        $this->command->info("Importazione nazioni da CSV...");
        
        // Array per mappare i codici continente
        $continenti = [
            'EU' => 'Europa',
            'AS' => 'Asia', 
            'NA' => 'Nord America',
            'SA' => 'Sud America',
            'AF' => 'Africa',
            'OC' => 'Oceania',
            'AN' => 'Antartide'
        ];
        
        // Leggi il CSV
        $handle = fopen($csvFile, 'r');
        $count = 0;
        
        while (($row = fgetcsv($handle, 1000, ',')) !== false) {
            // Salta righe vuote
            if (empty($row[1])) continue;
            
            try {
                // Crea il record nazione
                Nazione::create([
                    'nome' => trim($row[1]),
                    'continente' => $continenti[$row[2]] ?? $row[2],
                    'iso' => trim($row[3]),
                    'iso3' => trim($row[4]),
                    'prefissoTelefonico' => str_replace('+', '', trim($row[5])),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
                
                $count++;
                
            } catch (\Exception $e) {
                $this->command->warn("Errore importando riga: " . implode(',', $row) . " - " . $e->getMessage());
            }
        }
        
        fclose($handle);
        
        // Riabilita i controlli delle foreign key
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        
        $this->command->info("Importate {$count} nazioni con successo!");
    }
}
