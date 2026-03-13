<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('recalibraciones', function (Blueprint $table) {
            $table->id();
            $table->foreignId('stock_id')
                  ->constrained('stocks')
                  ->onDelete('cascade')
                  ->name('fk_recalibraciones_stock_id');  // ← NOMBRE EXPLÍCITO y único

            $table->date('fecha_recalibracion')
                  ->comment('Fecha en que se realizó la recalibración');

            $table->date('proxima_recalibracion')
                  ->nullable()
                  ->comment('Fecha estimada para la próxima recalibración');

            $table->text('observaciones')->nullable();

            // Elige UNA de estas dos opciones (no ambas):
            // Opción A: Nombre como texto (lo que usas en el formulario)
            $table->string('realizada_por_nombre', 100)->nullable();

            // Opción B: Relación real con usuario (mejor práctica a futuro)
            // $table->foreignId('user_id')->nullable()
            //       ->constrained('users')
            //       ->nullOnDelete()
            //       ->name('fk_recalibraciones_user_id');  // nombre explícito

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('recalibraciones');
    }
};