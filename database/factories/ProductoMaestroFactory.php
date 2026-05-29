<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class ProductoMaestroFactory extends Factory
{
    public function definition(): array
    {
        return [
            'sku' => 'PRD-' . fake()->unique()->numerify('######'),
            'nombre_comercial' => fake()->word() . ' ' . fake()->randomElement(['500mg', '1g', 'Jarabe', 'Gotas']),
            'nombre_generico' => fake()->word(),
            'descripcion' => fake()->sentence(),
            // Las llaves foráneas las pasaremos desde el Seeder para no crear duplicados
            'requiere_receta' => fake()->boolean(30),
            'registro_sanitario' => 'RS-' . fake()->bothify('?????-####'),
            'concentracion' => fake()->randomElement(['500 mg', '1 g', '20 ml', '50 mg/ml']),
            'forma_farmaceutica' => fake()->randomElement(['Tableta', 'Cápsula', 'Jarabe', 'Inyectable', 'Crema']),
            'unidad_medida' => fake()->randomElement(['Caja', 'Frasco', 'Blister', 'Tubo']),
            'stock_minimo' => fake()->numberBetween(10, 50),
            'activo' => true,
        ];
    }
}