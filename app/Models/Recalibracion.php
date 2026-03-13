<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Recalibracion extends Model
{
    use HasFactory;

    protected $table = 'recalibraciones';  // ← CAMBIO IMPORTANTE: nombre correcto en español

    protected $fillable = [
        'stock_id',
        'fecha_recalibracion',
        'proxima_recalibracion',
        'observaciones',
        'realizada_por_nombre',   // ← AGREGADO: coincide exactamente con el campo del Repeater
    ];

    protected $casts = [
        'fecha_recalibracion'    => 'date',
        'proxima_recalibracion'  => 'date',
    ];

    public function stock(): BelongsTo
    {
        return $this->belongsTo(Stock::class);
    }

    // Si más adelante quieres relacionar con usuario real (opcional)
    // public function user(): BelongsTo
    // {
    //     return $this->belongsTo(User::class);
    // }
}