<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('paiements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('contrat_id')
                ->constrained('contrats')
                ->cascadeOnDelete();

            $table->decimal('montant', 15, 2);
            $table->date('date_echeance');
            $table->date('date_paiement')->nullable();

            $table->enum('statut', ['en_attente', 'paye', 'en_retard'])
                ->default('en_attente')
                ->index();

            $table->enum('methode', ['mobile_money', 'cash', 'carte_bancaire'])->nullable();
            $table->string('operateur', 50)->nullable(); // ex: "Wave", "Orange Money"
            $table->string('reference_transaction', 100)->nullable(); // ID transaction opérateur

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('paiements');
    }
};
