<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('codificacions', function (Blueprint $table) {
            $table->id();
            $table->string('codificacion')->unique();   // Ej: EPPs, Útiles de escritorio, Herramientas
            $table->string('codigo')->unique();         // Ej: EPP, UES, HER (el prefijo o código corto que vos decidís)
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('codificacions');
    }
};