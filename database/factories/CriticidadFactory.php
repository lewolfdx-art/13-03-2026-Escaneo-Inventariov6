<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class CriticidadFactory extends Factory
{
    public function definition(): array
    {
        return [
            'nombre'      => $this->faker->randomElement([
                'Costo', 'Frecuencia', 'Cumplimiento', 'Riesgo'
            ]),
            'codigo'      => $this->faker->unique()->lexify('???'),
            'descripcion' => $this->faker->sentence(10),
            'condicion'   => $this->faker->randomElement([
                '>= 15000 dólares', 'Alta', 'Sí (normativa obligatoria)', 'Alto'
            ]),
            'peso'        => $this->faker->numberBetween(1, 4),
            'aplica_a'    => 'Equipos,Insumos,Materiales,Otros',
            'activo'      => true,
        ];
    }
}