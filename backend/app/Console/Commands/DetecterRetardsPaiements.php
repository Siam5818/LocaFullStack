<?php
/* cspell:disable */

namespace App\Console\Commands;

use App\Services\PaiementService;
use Illuminate\Console\Command;

class DetecterRetardsPaiements extends Command
{
    protected $signature = 'paiements:detecter-retards';
    protected $description = 'Marque comme en_retard les paiements dont la date échéance est dépassée.';

    public function handle(PaiementService $paiementService): int
    {
        $nb = $paiementService->detecterRetards();
        $this->info("Paiements marqués en retard : {$nb}");
        return Command::SUCCESS;
    }
}
