<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('proprietes', function (Blueprint $table) {
            $table->id();
            $table->string('nom', 200);

            // Adresse décomposée
            $table->string('rue', 255)->nullable();
            $table->string('quartier', 100)->nullable();
            $table->string('ville', 100);
            $table->string('pays', 100)->default('Sénégal');
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();

            $table->unsignedInteger('nombre_piece');
            $table->unsignedInteger('dimension'); // en m²
            $table->text('description');
            $table->decimal('cout', 15, 2);

            $table->foreignId('typepropriete_id')->constrained('typeproprietes');
            $table->foreignId('bailleur_id')->constrained('bailleurs');
            $table->foreignId('service_id')->constrained('services');

            $table->timestamps();
            $table->softDeletes();

            $table->index(['ville', 'quartier']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('proprietes');
    }
};
