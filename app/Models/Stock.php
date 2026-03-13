<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Stock extends Model
{
    /** @use HasFactory<\Database\Factories\StockFactory> */
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
        'stock_actual',
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
        'stock_actual'   => 'integer',
    ];

    // ────────────────────────────────────────────────
    // Relaciones
    // ────────────────────────────────────────────────

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

    // Opcional: accessor para mostrar algo útil en listas
    public function getNombreCompletoAttribute(): string
    {
        return trim("{$this->codigo} - {$this->descripcion}");
    }

    /**
     * Genera el siguiente código secuencial para una codificación dada
     * Ej: EPP-001, EPP-002, UES-001, HER-003, etc.
     *
     * @param int $codificacionId
     * @return string
     */
    public static function generarSiguienteCodigo(int $codificacionId): string
    {
        $codificacion = Codificacion::findOrFail($codificacionId);
        $prefijo = $codificacion->codigo; // ej: 'EPP', 'UES', 'HER'

        // Buscar el último código con este prefijo
        $ultimoCodigo = self::where('codificacion_id', $codificacionId)
            ->where('codigo', 'like', $prefijo . '-%')
            ->orderByRaw("CAST(SUBSTRING_INDEX(codigo, '-', -1) AS UNSIGNED) DESC")
            ->value('codigo');

        $numero = 1;

        if ($ultimoCodigo) {
            // Extraer el número después del último guión
            $numero = (int) substr($ultimoCodigo, strrpos($ultimoCodigo, '-') + 1) + 1;
        }

        // Formato con 3 dígitos (001, 002, ..., 010, 100, etc.)
        return sprintf('%s-%03d', $prefijo, $numero);
    }

    // ────────────────────────────────────────────────
    // ACCESSORS NUEVOS PARA LA COLUMNA EN FILAMENT
    // ────────────────────────────────────────────────

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