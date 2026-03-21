<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('inventarios', function (Blueprint $table) {
            $table->string('modelo')->nullable()->after('stock_conteo');
            $table->string('numero_serie')->nullable()->after('modelo');
            $table->string('talla')->nullable()->after('numero_serie');
            $table->boolean('tiene_codigo_barras')->default(false)->after('talla');
        });
    }

    public function down(): void
    {
        Schema::table('inventarios', function (Blueprint $table) {
            $table->dropColumn(['modelo', 'numero_serie', 'talla', 'tiene_codigo_barras']);
        });
    }
};