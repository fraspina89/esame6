<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use App\Models\Contatto;
use App\Models\ContattoAuth;
use App\Models\ContattoPassword;
use App\Models\ContattoSessione;
use Illuminate\Support\Facades\DB;

class CleanTestData extends Command
{
    protected $signature = 'clean:test-data {--dry-run} {--force} {--domain= : Email domain to target (default: example.com)} {--days=30 : Lookback window in days for created_at}';

    protected $description = 'Backup and remove test/demo contact data (contatti, contattiAuth, contattiPassword, contattiSessioni, pivots). Use --dry-run first.';

    public function handle()
    {
        $domain = $this->option('domain') ?? 'example.com';
        $days = (int)$this->option('days');
        if ($days <= 0) {
            $days = 30;
        }

        $this->info("Criteria: email domain = {$domain}; lookback days = {$days}");

        $cutoff = now()->subDays($days);

        $query = Contatto::query()
            ->leftJoin('contattiAuth', 'contatti.idContatto', '=', 'contattiAuth.idContatto')
            ->where(function ($q) use ($domain, $cutoff) {
                $q->where('contattiAuth.user', 'like', "%@{$domain}")
                  ->orWhere('contatti.created_at', '>=', $cutoff);
            })
            ->select('contatti.*');

        $contatti = $query->distinct()->get();

        if ($contatti->isEmpty()) {
            $this->info('Nessun contatto di prova trovato con i criteri specificati.');
            return 0;
        }

        $ids = $contatti->pluck('idContatto')->toArray();

        $this->info('Contatti trovati: ' . count($ids));

        $backup = [
            'meta' => [
                'domain' => $domain,
                'days' => $days,
                'timestamp' => now()->toDateTimeString(),
            ],
            'contatti' => $contatti->toArray(),
            'contatti_auth' => ContattoAuth::whereIn('idContatto', $ids)->get()->toArray(),
            'contatti_password' => ContattoPassword::whereIn('idContatto', $ids)->get()->toArray(),
            'contatti_sessioni' => ContattoSessione::whereIn('idContatto', $ids)->get()->toArray(),
            'pivots' => DB::table('contatti_contattiruolo')->whereIn('idContatto', $ids)->get()->toArray(),
        ];

        $fs = new Filesystem();
        $storagePath = storage_path('app/clean_test_data_backup_' . now()->format('Ymd_His') . '.json');
        $fs->ensureDirectoryExists(dirname($storagePath));
        $fs->put($storagePath, json_encode($backup, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

        $this->info("Backup scritto in: {$storagePath}");

        if ($this->option('dry-run')) {
            $this->info('Dry-run: nessuna cancellazione eseguita.');
            return 0;
        }

        if (! $this->option('force')) {
            $this->error('Per procedere aggiungi l`opzione --force');
            return 1;
        }

        DB::beginTransaction();
        try {
            // delete pivots
            DB::table('contatti_contattiruolo')->whereIn('idContatto', $ids)->delete();

            ContattoSessione::whereIn('idContatto', $ids)->delete();
            ContattoPassword::whereIn('idContatto', $ids)->delete();
            ContattoAuth::whereIn('idContatto', $ids)->delete();
            Contatto::whereIn('idContatto', $ids)->delete();

            DB::commit();
            $this->info('Cancellazione completata con successo.');
            $this->info('Backup originale conservato per recupero.');
            return 0;
        } catch (\Exception $e) {
            DB::rollBack();
            $this->error('Errore durante la cancellazione: ' . $e->getMessage());
            return 1;
        }
    }
}
