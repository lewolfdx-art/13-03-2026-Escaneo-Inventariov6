<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class MedidaFactory extends Factory
{
    public function definition(): array
    {
        return [
            'nombre' => $this->faker->unique()->randomElement([
                'PZA', 'UNIDAD', 'KG', 'GRAMO', 'LT', 'ML', 'M', 'CM', 'MM',
                'SET', 'CAJA', 'PAQ', 'ROLLO', 'JUEGO', 'KIT', 'PAR', 'DOCENA',
                'LITRO', 'METRO', 'PIEZA', 'BOTELLA', 'GALÓN'
            ]),
        ];
    }
}