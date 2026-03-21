<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('inventarios', function (Blueprint $table) {
            // Eliminar columnas que ya no se usan
            $table->dropColumn('stock_conteo');
            $table->dropColumn('diferencia');

            // Opcional: cambiar fecha_conteo de date a dateTime (para guardar hora exacta)
            // Solo hazlo si quieres consistencia con el modelo (que usa 'datetime')
            // $table->dateTime('fecha_conteo')->change();
        });
    }

    public function down(): void
    {
        Schema::table('inventarios', function (Blueprint $table) {
            // Restaurar columnas si reviertes (útil para rollback)
            $table->unsignedInteger('stock_conteo')->nullable()->after('stock_sistema');
            $table->integer('diferencia')->nullable()->after('stock_conteo');

            // Si cambiaste a dateTime, revierte aquí
            // $table->date('fecha_conteo')->change();
        });
    }
};