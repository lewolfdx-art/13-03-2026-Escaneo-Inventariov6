<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Kardex extends Model
{
    use HasFactory;

    protected $fillable = [
        'stock_id',
        'fecha',
        'tipo_movimiento',
        'cantidad',
        'medida_id',  // ← Nueva
        'proyecto',
        'servicio',
        'area',
        'entregado_a',
        'observacion',
        'referencia_documento',
        'saldo_anterior',
        'saldo_actual',
        'costo_unitario',
        'costo_total',
        'user_id',
    ];

    protected $casts = [
        'fecha'           => 'date',
        'cantidad'        => 'integer',
        'saldo_anterior'  => 'integer',
        'saldo_actual'    => 'integer',
        'costo_unitario'  => 'decimal:4',
        'costo_total'     => 'decimal:4',
    ];

    protected static function booted()
    {
        static::creating(function (Kardex $kardex) {
            $stock = $kardex->stock;

            $kardex->saldo_anterior = $stock->stock_actual;

            $delta = match ($kardex->tipo_movimiento) {
                'entrada'   => $kardex->cantidad,
                'salida'    => -$kardex->cantidad,
                default     => 0,
            };

            $nuevoSaldo = max(0, $kardex->saldo_anterior + $delta);
            $kardex->saldo_actual = $nuevoSaldo;

            $stock->update(['stock_actual' => $nuevoSaldo]);
        });
    }

    public function stock(): BelongsTo
    {
        return $this->belongsTo(Stock::class);
    }

    public function medida(): BelongsTo
    {
        return $this->belongsTo(Medida::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function getSignAttribute(): string
    {
        return match ($this->tipo_movimiento) {
            'entrada' => '+',
            'salida'  => '-',
            default   => '',
        };
    }

    public function getCantidadConSignoAttribute(): string
    {
        return $this->sign . $this->cantidad;
    }
}