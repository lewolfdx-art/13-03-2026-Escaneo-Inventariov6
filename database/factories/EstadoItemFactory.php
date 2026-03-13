<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class EstadoItemFactory extends Factory
{
    public function definition(): array
    {
        return [
            'nombre'      => $this->faker->randomElement(['Bueno', 'Regular', 'Malo', 'Deshecho']),
            'slug'        => $this->faker->slug(),
            'descripcion' => $this->faker->sentence(12),
            'color'       => $this->faker->randomElement(['success', 'warning', 'danger', 'gray']),
            'prioridad'   => $this->faker->numberBetween(1, 10),
            'activo'      => true,
        ];
    }
}