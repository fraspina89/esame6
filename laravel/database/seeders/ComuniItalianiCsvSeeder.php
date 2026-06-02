<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ComuniItalianiCsvSeeder extends Seeder
{
    public function run()
    {
        $path = database_path('seeders/data/comuniItaliani.csv');
        if (!file_exists($path)) {
            $this->command->error('CSV file not found: ' . $path);
            return;
        }

        $handle = fopen($path, 'r');
        if ($handle === false) {
            $this->command->error('Unable to open CSV file');
            return;
        }

        $batch = [];
        $count = 0;
        while (($row = fgetcsv($handle, 0, ',')) !== false) {
            // expected columns: id, nome, regione, provincia, ?, sigla_provincia, codice_catastale, abitanti, superficie, cap, cap_finale, cap_iniziale
            $batch[] = [
                'comune' => $row[1] ?? null,
                'regione' => $row[2] ?? null,
                'provincia' => $row[3] ?? null,
                'zona' => '',
                'sigla_provincia' => $row[5] ?? null,
                'codice_istat' => $row[6] ?? null,
                'abitanti' => is_numeric($row[7] ?? null) ? (int)$row[7] : 0,
                'superficie' => is_numeric($row[8] ?? null) ? (float)$row[8] : 0,
                'cap' => $row[9] ?? null,
                'cap_finale' => is_numeric($row[10] ?? null) ? (int)$row[10] : 0,
                'cap_iniziale' => is_numeric($row[11] ?? null) ? (int)$row[11] : 0,
                'created_at' => now(),
                'updated_at' => now(),
            ];

            if (count($batch) >= 500) {
                DB::table('comuni_italiani')->insert($batch);
                $count += count($batch);
                $batch = [];
                $this->command->info("Inserted $count rows...");
            }
        }

        if (count($batch) > 0) {
            DB::table('comuni_italiani')->insert($batch);
            $count += count($batch);
        }

        fclose($handle);
        $this->command->info("Comuni importati: $count");
    }
}
