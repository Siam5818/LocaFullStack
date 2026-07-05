<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('typecontrats', function (Blueprint $table) {
            $table->id();
            $table->string('nom', 100)->unique(); // ex: "Bail 1 an", "Vente définitive"
            $table->integer('duree_mois')->nullable(); // null si vente (pas de durée)
            $table->decimal('taux_commission_defaut', 5, 2)->default(10.00); // en %, ex: 10.00 = 10%
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('typecontrats');
    }
};