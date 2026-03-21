<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('inventarios', function (Blueprint $table) {
            $table->id();
            $table->foreignId('stock_id')->constrained('stocks')->cascadeOnDelete();
            $table->date('fecha_conteo')->useCurrent(); // Fecha automática del conteo/escaneo
            $table->integer('stock_sistema')->unsigned(); // Stock calculado desde Kardex en ese momento
            $table->integer('stock_conteo')->unsigned()->nullable(); // Lo que se contó físicamente (puede ser null si solo visualización)
            $table->integer('diferencia')->nullable(); // stock_conteo - stock_sistema (positivo = sobrante, negativo = faltante)
            $table->text('observaciones')->nullable();
            $table->foreignId('user_id')->constrained('users')->nullable(); // Quién hizo el conteo
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inventarios');
    }
};