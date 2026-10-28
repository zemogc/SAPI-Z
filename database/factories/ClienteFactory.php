<?php

namespace Database\Factories;

use App\Models\Cliente;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Cliente>
 */
class ClienteFactory extends Factory
{
    private const NOMBRES = ['Valentina', 'Santiago', 'Camila', 'Sebastián', 'Mariana', 'Andrés', 'Daniela', 'Juan Pablo', 'Laura', 'Felipe', 'Isabella', 'Mateo', 'Natalia', 'Carlos', 'Sofía', 'Alejandro', 'Paula', 'Diego', 'Juliana', 'Nicolás'];

    private const APELLIDOS = ['Rodríguez', 'Gómez', 'Martínez', 'García', 'López', 'Hernández', 'Ramírez', 'Torres', 'Castro', 'Vargas', 'Rojas', 'Moreno', 'Jiménez', 'Ospina', 'Restrepo', 'Cárdenas', 'Mejía', 'Zapata'];

    public function definition(): array
    {
        $nombre = fake()->randomElement(self::NOMBRES);
        $apellido = fake()->randomElement(self::APELLIDOS);

        return [
            'nombre' => $nombre,
            'apellido' => $apellido,
            'telefono' => '+57 3'.fake()->numerify('## ### ####'),
            'email' => Str::slug("{$nombre}.{$apellido}", '.').fake()->unique()->numberBetween(1, 999).'@'.fake()->randomElement(['gmail.com', 'outlook.com', 'yahoo.com']),
            'tipo' => fake()->randomElement(Cliente::TIPOS),
        ];
    }
}
