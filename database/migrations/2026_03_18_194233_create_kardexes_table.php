<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kardexes', function (Blueprint $table) {
            $table->id();

            $table->foreignId('stock_id')
                ->constrained('stocks')
                ->onDelete('cascade')
                ->index();

            $table->date('fecha')->index();

            $table->enum('tipo_movimiento', ['entrada', 'salida'])
                ->default('entrada');

            $table->unsignedInteger('cantidad');

            $table->foreignId('medida_id')  // ← Nueva: unidad de medida
                ->nullable()
                ->constrained('medidas')
                ->onDelete('set null');

            // Saldos automáticos
            $table->integer('saldo_anterior')->nullable();
            $table->integer('saldo_actual')->nullable();

            // Campos separados (en vez de uno solo)
            $table->string('proyecto', 100)->nullable();
            $table->string('servicio', 100)->nullable();
            $table->string('area', 100)->nullable();

            $table->string('entregado_a', 120)->nullable();

            $table->text('observacion')->nullable();
            $table->string('referencia_documento', 100)->nullable();

            $table->decimal('costo_unitario', 12, 4)->nullable();
            $table->decimal('costo_total', 14, 4)->nullable();

            $table->foreignId('user_id')
                ->nullable()
                ->constrained('users')
                ->onDelete('set null');

            $table->timestamps();

            $table->index(['stock_id', 'fecha']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kardexes');
    }
};