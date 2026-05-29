<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Carbon\Carbon;

class LoteFactory extends Factory
{
    public function definition(): array
    {
        $fechaFabricacion = fake()->dateTimeBetween('-2 years', 'now');
        $cantidad = fake()->numberBetween(50, 500);

        return [
            // producto_id se pasa en el seeder
            'numero_lote' => 'LT-' . fake()->unique()->bothify('??####'),
            'fecha_fabricacion' => $fechaFabricacion,
            'fecha_ingreso' => Carbon::instance($fechaFabricacion)->addDays(rand(10, 30)),
            'fecha_vencimiento' => Carbon::instance($fechaFabricacion)->addYears(rand(2, 5)),
            'cantidad_inicial' => $cantidad,
            'cantidad_actual' => fake()->numberBetween(0, $cantidad),
            'costo_unitario' => fake()->randomFloat(2, 1, 150),
            'estado' => fake()->randomElement(['disponible', 'disponible', 'cuarentena']), // Más chance de que esté disponible
        ];
    }
}