<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class SedeFactory extends Factory
{
    public function definition(): array
    {
        return [
            'codigo' => 'SD-' . fake()->unique()->numerify('####'),
            'nombre' => 'Sede ' . fake()->city(),
            'direccion' => fake()->streetAddress(),
            'telefono' => fake()->numerify('9########'),
            'activo' => fake()->boolean(90),
        ];
    }
}