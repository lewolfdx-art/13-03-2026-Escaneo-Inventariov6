<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('estado_items', function (Blueprint $table) {
            $table->id();
            $table->string('nombre')->unique();          // Bueno, Regular, Malo, Deshecho
            $table->string('slug')->unique()->nullable(); // bueno, regular, malo, deshecho (para usar en URLs o keys)
            $table->text('descripcion')->nullable();
            $table->string('color')->nullable();         // Para badges en Filament: success, warning, danger, gray
            $table->integer('prioridad')->default(10);   // Orden: menor número = más urgente (ej. Deshecho=1, Bueno=4)
            $table->boolean('activo')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('estado_items');
    }
};