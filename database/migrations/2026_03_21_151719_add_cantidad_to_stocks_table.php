<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
{
    Schema::table('stocks', function (Blueprint $table) {
        $table->unsignedInteger('cantidad')
              ->default(1)
              ->after('disponible')
              ->comment('Cantidad actual disponible de esta herramienta');
    });
}

public function down(): void
{
    Schema::table('stocks', function (Blueprint $table) {
        $table->dropColumn('cantidad');
    });
}
};
