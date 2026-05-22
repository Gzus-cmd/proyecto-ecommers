<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class LaboratorioFactory extends Factory
{
    public function definition(): array
    {
        return [
            'nombre' => fake()->company() . ' Pharma',
            'pais' => fake()->country(),
        ];
    }
}