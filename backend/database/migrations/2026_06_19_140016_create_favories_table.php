<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('favories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_id')
                ->constrained('clients')
                ->cascadeOnDelete();
            $table->foreignId('propriete_id')
                ->constrained('proprietes')
                ->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['client_id', 'propriete_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('favories');
    }
};
