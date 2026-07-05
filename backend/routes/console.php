<?php
/* cspell:disable */

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;
use App\Services\PaiementService;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Tourne tous les jours à minuit pour détecter les paiements en retard.
Schedule::call(function () {
    $service = app(PaiementService::class);
    $nb = $service->detecterRetards();
    logger()->info("Paiements marqués en retard : {$nb}");
})->daily()->name('detecter-retards-paiements')->withoutOverlapping();
