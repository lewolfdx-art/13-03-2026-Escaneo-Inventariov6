<?php

namespace Database\Factories;

use App\Models\Kardex;
use App\Models\Stock;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class KardexFactory extends Factory
{
    protected $model = Kardex::class;

    public function definition(): array
    {
        $stock = Stock::inRandomOrder()->first() ?? Stock::factory()->create();
        $user  = User::inRandomOrder()->first() ?? User::factory()->create();

        $esEntrada = $this->faker->boolean(60); // 60% entradas, 40% salidas (ajusta si quieres)
        $tipo = $esEntrada ? 'entrada' : 'salida';

        $cantidad = $this->faker->numberBetween(1, 50);

        // Simulamos saldo previo (en producción esto se calcula automáticamente en el modelo)
        $saldoAnterior = $this->faker->numberBetween(5, 300);
        $delta = $esEntrada ? $cantidad : -$cantidad;
        $saldoNuevo = max(0, $saldoAnterior + $delta);

        return [
            'stock_id'                => $stock->id,
            'fecha'                   => $this->faker->dateTimeBetween('-4 months', 'now'),
            'tipo_movimiento'         => $tipo,
            'cantidad'                => $cantidad,
            'saldo_anterior'          => $saldoAnterior,
            'saldo_actual'            => $saldoNuevo,

            'entregado_a'             => $esEntrada ? null : $this->faker->name(),
            'proyecto_area_servicio'  => $this->faker->randomElement([
                null,
                'CONSTABLE',
                'Proyecto Vial Huancayo',
                'Mantenimiento Sedalibre',
                'Área Técnica Junín',
                'Taller Central',
            ]),

            'observacion'             => $this->faker->optional(0.7)->sentence(4 . $this->faker->numberBetween(4, 12)),

            'referencia_documento'    => $this->faker->optional(0.5)->randomElement([
                'OC-' . $this->faker->numberBetween(10000, 99999),
                'GR-' . $this->faker->numberBetween(5000, 99999),
                'VALE-' . $this->faker->numberBetween(100, 9999),
            ]),

            'costo_unitario'          => $esEntrada ? $this->faker->randomFloat(2, 8, 450) : null,
            'costo_total'             => $esEntrada ? fn ($attr) => round($attr['cantidad'] * $attr['costo_unitario'], 2) : null,

            'user_id'                 => $user->id,
        ];
    }
}