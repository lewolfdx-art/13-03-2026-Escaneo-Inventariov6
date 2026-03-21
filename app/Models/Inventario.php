<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $stock_id
 * @property \Illuminate\Support\Carbon|null $fecha_conteo
 * @property int|null $stock_sistema
 * @property string|null $observaciones
 * @property int|null $user_id
 * @property string|null $modelo
 * @property string|null $numero_serie
 * @property string|null $talla
 * @property bool $tiene_codigo_barras
 * @property-read \App\Models\Stock|null $stock
 * @property-read \App\Models\User|null $user
 * @property-read string $marca
 *
 * @method static \Database\Factories\InventarioFactory factory($count = null, $state = [])
 */
class Inventario extends Model
{
    use HasFactory;

    // ... el resto del código exactamente igual ...

    protected $fillable = [
        'stock_id',
        'fecha_conteo',
        'stock_sistema',
        'observaciones',
        'user_id',
        'modelo',
        'numero_serie',
        'talla',
        'tiene_codigo_barras',
    ];

    protected $casts = [
        'fecha_conteo'        => 'datetime',
        'stock_sistema'       => 'integer',
        'tiene_codigo_barras' => 'boolean',
    ];

    public function stock(): BelongsTo
    {
        return $this->belongsTo(Stock::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function getMarcaAttribute(): string
    {
        return $this->stock?->marca?->nombre ?? '—';
    }

    protected static function booted(): void
    {
        static::creating(function (Inventario $inventario): void {
            $inventario->fecha_conteo  = now();
            $inventario->stock_sistema = $inventario->stock?->stock_actual ?? 0;
    
            // Asignación limpia y que Intelephense entiende bien
            $inventario->user_id = \Illuminate\Support\Facades\Auth::id() ?? null;
    
            // Copia de valores del stock solo si no fueron ingresados manualmente
            $inventario->modelo       = $inventario->modelo       ?? $inventario->stock?->modelo       ?? null;
            $inventario->numero_serie = $inventario->numero_serie ?? $inventario->stock?->numero_serie ?? null;
            $inventario->talla        = $inventario->talla        ?? $inventario->stock?->talla        ?? null;
        });
    
        // Si más adelante necesitas lógica en updating, agrégala aquí
        // static::updating(function (Inventario $inventario): void { ... });
    }
}