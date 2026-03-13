<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('stocks', function (Blueprint $table) {
            $table->id();

            $table->string('codigo', 50)->unique()->index();           // CODIGO (único) + índice para búsquedas rápidas
            $table->string('descripcion', 255)->index();               // DESCRIPCION

            $table->foreignId('medida_id')
                ->constrained('medidas')
                ->onDelete('restrict');

            $table->foreignId('codificacion_id')                       // ← cambio principal
                ->constrained('codificacions')
                ->onDelete('restrict');

            $table->foreignId('marca_id')
                ->constrained('marcas')
                ->onDelete('restrict');

            $table->string('modelo', 100)->nullable();
            $table->string('numero_serie', 100)->nullable();
            $table->string('talla', 50)->nullable();

            $table->integer('stock_minimo')->default(0);
            $table->integer('stock_actual')->default(0);

            $table->string('condicion', 50)->nullable();
            $table->date('ultima_compra')->nullable();

            $table->boolean('es_critico')->default(false);

            $table->string('estado', 50)->nullable();

            $table->text('observaciones')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stocks');
    }
};