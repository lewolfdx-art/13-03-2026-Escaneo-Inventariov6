<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('kardexes', function (Blueprint $table) {
            $table->foreignId('medida_id')
                ->nullable()
                ->constrained('medidas')
                ->onDelete('set null')
                ->after('cantidad');  // Puedes cambiar 'after' si querés otra posición
        });
    }

    public function down(): void
    {
        Schema::table('kardexes', function (Blueprint $table) {
            $table->dropForeign(['medida_id']);
            $table->dropColumn('medida_id');
        });
    }
};