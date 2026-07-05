<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bailleurs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('personne_id')
                ->unique()
                ->constrained('personnes')
                ->cascadeOnDelete();

            // Coordonnées bancaires pour reversement des loyers/ventes
            $table->string('iban', 50)->nullable();
            $table->string('nom_banque', 100)->nullable();

            // Traçabilité : seul un admin certifié peut créer un bailleur
            $table->foreignId('created_by_admin_id')
                ->constrained('admins')
                ->restrictOnDelete(); // empêche de supprimer un admin qui a créé des bailleurs

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bailleurs');
    }
};
