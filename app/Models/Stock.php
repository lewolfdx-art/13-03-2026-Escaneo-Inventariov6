<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\DB;
class Stock extends Model
{
    use HasFactory;

    protected $fillable = [
        'codigo',
        'descripcion',
        'medida_id',
        'codificacion_id',
        'marca_id',
        'modelo',
        'numero_serie',
        'talla',
        'stock_minimo',
        'condicion',
        'ultima_compra',
        'es_critico',
        'estado',
        'observaciones',
    ];

    protected $casts = [
        'ultima_compra'  => 'date',
        'es_critico'     => 'boolean',
        'stock_minimo'   => 'integer',
    ];

    // Relaciones
    public function medida(): BelongsTo
    {
        return $this->belongsTo(Medida::class);
    }

    public function codificacion(): BelongsTo
    {
        return $this->belongsTo(Codificacion::class);
    }

    public function marca(): BelongsTo
    {
        return $this->belongsTo(Marca::class);
    }

    public function recalibraciones(): HasMany
    {
        return $this->hasMany(Recalibracion::class);
    }

    public function kardexes(): HasMany
    {
        return $this->hasMany(Kardex::class);
    }

    // Stock actual CALCULADO desde Kardex (la fuente de verdad)
    public function getStockActualAttribute(): int
    {
        return (int) $this->kardexes()
            ->sum(DB::raw("CASE WHEN tipo_movimiento = 'entrada' THEN cantidad ELSE -cantidad END"));
    }

    // Nombre completo para listas
    public function getNombreCompletoAttribute(): string
    {
        return trim("{$this->codigo} - {$this->descripcion}");
    }

    // Generador de código (ya lo tenías, está bien)
    public static function generarSiguienteCodigo(int $codificacionId): string
    {
        $codificacion = Codificacion::findOrFail($codificacionId);
        $prefijo = $codificacion->codigo;

        $ultimoCodigo = self::where('codificacion_id', $codificacionId)
            ->where('codigo', 'like', $prefijo . '-%')
            ->orderByRaw("CAST(SUBSTRING_INDEX(codigo, '-', -1) AS UNSIGNED) DESC")
            ->value('codigo');

        $numero = 1;
        if ($ultimoCodigo) {
            $numero = (int) substr($ultimoCodigo, strrpos($ultimoCodigo, '-') + 1) + 1;
        }

        return sprintf('%s-%03d', $prefijo, $numero);
    }

    // Accessors para recalibraciones (los mantengo)
    public function getProximaRecalibracionFormattedAttribute(): string
    {
        $ultima = $this->recalibraciones()->latest('fecha_recalibracion')->first();
        return $ultima?->proxima_recalibracion?->format('d/m/Y') ?? 'Sin programar';
    }

    public function getProximaRecalibracionColorAttribute(): string
    {
        $ultima = $this->recalibraciones()->latest('fecha_recalibracion')->first();
        $proxima = $ultima?->proxima_recalibracion;

        if (!$proxima) return 'gray';
        if ($proxima->isPast()) return 'danger';
        if ($proxima->diffInDays() <= 30) return 'warning';
        return 'success';
    }
}