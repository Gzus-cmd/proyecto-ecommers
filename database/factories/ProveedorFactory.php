<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class ProveedorFactory extends Factory
{
    public function definition(): array
    {
        return [
            'razon_social' => fake()->company() . ' S.A.C.',
            'ruc' => '20' . fake()->unique()->numerify('#########'),
            'contacto' => fake()->name(),
            'telefono' => fake()->numerify('9########'),
            'email' => fake()->unique()->companyEmail(),
            'direccion' => fake()->address(),
            'activo' => true,
        ];
    }
}
