<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('contrats', function (Blueprint $table) {
            $table->id();
            $table->foreignId('reservation_id')
                ->unique()
                ->constrained('reservations')
                ->cascadeOnDelete();
            $table->foreignId('typecontrat_id')->constrained('typecontrats');

            $table->date('date_debut');
            $table->date('date_fin')->nullable(); // null si vente définitive

            // Montants : valeurs figées au moment de la signature, ne dépendent
            // plus du taux par défaut de typecontrats même s'il change après.
            $table->decimal('montant_total', 15, 2);
            $table->decimal('taux_commission_applique', 5, 2);
            $table->decimal('montant_commission', 15, 2);

            // Pertinent uniquement pour les contrats de vente
            $table->enum('mode_paiement_vente', ['unique', 'echelonne'])->nullable();

            $table->enum('statut', ['actif', 'termine', 'resilie'])->default('actif');

            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('contrats');
    }
};
