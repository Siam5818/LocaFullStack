<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('propriete_equipement', function (Blueprint $table) {
            $table->id();
            $table->foreignId('propriete_id')
                ->constrained('proprietes')
                ->cascadeOnDelete();
            $table->foreignId('equipement_id')
                ->constrained('equipements')
                ->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['propriete_id', 'equipement_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('propriete_equipement');
    }
};
