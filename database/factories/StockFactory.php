<?php

namespace Database\Factories;

use App\Models\Codificacion;
use App\Models\Marca;
use App\Models\Medida;
use App\Models\Stock;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Stock>
 */
class StockFactory extends Factory
{
    public function definition(): array
    {
        $codificacion = Codificacion::inRandomOrder()->first() ?? Codificacion::factory()->create();
        $marca        = Marca::inRandomOrder()->first() ?? Marca::factory()->create();
        $medida       = Medida::inRandomOrder()->first() ?? Medida::factory()->create();

        $prefijo = $codificacion->codigo ?? 'XXX';

        return [
            'codigo'          => $this->faker->unique()->bothify($prefijo . '-#####'),
            'descripcion'     => $this->faker->randomElement([
                'Guantes de nitrilo talla M',
                'Taladro percutor 850W',
                'Botas de seguridad punta de acero',
                'Caja de tornillos autorroscantes 4x25',
                'Mascarilla FFP2 KN95',
                'Destornillador plano 6"',
                'Lápices HB caja x12',
                'Cable eléctrico 2.5mm² rollo 100m',
            ]),
            'medida_id'       => $medida->id,
            'codificacion_id' => $codificacion->id,
            'marca_id'        => $marca->id,

            'modelo'          => $this->faker->randomElement([null, 'PRO-450', 'XTR-2024', 'STD-300', 'CLASSIC']),
            'numero_serie'    => $this->faker->optional(0.6)->numerify('SN-##########'),
            'talla'           => $this->faker->optional(0.4)->randomElement(['S', 'M', 'L', 'XL', 'XXL', '38', '40', '42']),

            'stock_minimo'    => $this->faker->numberBetween(5, 50),
            'stock_actual'    => $this->faker->numberBetween(0, 300),

            'condicion'       => $this->faker->randomElement(['Bueno', 'Regular', 'Malo', 'Nuevo', 'En reparación']),
            'ultima_compra'   => $this->faker->optional(0.7)->dateTimeBetween('-6 months', 'now')->format('Y-m-d'),

            'es_critico'      => fn (array $attrs) => $attrs['stock_actual'] <= $attrs['stock_minimo'],
            'estado'          => $this->faker->randomElement(['Activo', 'Activo', 'Inactivo', 'Baja']),

            'observaciones'   => $this->faker->optional(0.35)->sentence(6),
        ];
    }
}