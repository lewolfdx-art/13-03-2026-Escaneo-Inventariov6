<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('criticidads', function (Blueprint $table) {
            $table->id();
            
            $table->string('nombre')->unique();          // Ej: "Costo", "Frecuencia", "Cumplimiento", "Riesgo"
            $table->string('codigo')->unique()->nullable(); // Ej: "COS", "FRE", "CUM", "RIE" (opcional, para referencias cortas)
            $table->text('descripcion')->nullable();
            $table->string('condicion')->nullable();     // Ej: ">= 15000 dólares", "Alta", "Sí", "Alto"
            $table->integer('peso')->default(1);         // Puntuación para cálculo automático futuro (ej. Costo=3, Riesgo=3)
            $table->string('aplica_a')->nullable();      // Ej: "Equipos,Insumos,Materiales,Otros" o JSON/array
            
            $table->boolean('activo')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('criticidads');
    }
};