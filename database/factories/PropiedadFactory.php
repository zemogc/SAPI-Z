<?php

namespace Database\Factories;

use App\Models\Propiedad;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Propiedad>
 */
class PropiedadFactory extends Factory
{
    /**
     * Ciudad => [barrios, precio base por m² en COP].
     */
    public const CIUDADES = [
        'Cali' => [['San Fernando', 'Ciudad Jardín', 'El Peñón', 'Granada', 'Valle del Lili', 'Santa Teresita'], 5_200_000],
        'Bogotá' => [['Chapinero', 'Usaquén', 'Cedritos', 'La Candelaria', 'Teusaquillo', 'Rosales'], 7_400_000],
        'Medellín' => [['El Poblado', 'Laureles', 'Envigado', 'Belén', 'Sabaneta'], 6_600_000],
        'Barranquilla' => [['Alto Prado', 'Riomar', 'El Golf', 'Villa Country'], 4_900_000],
        'Cartagena' => [['Bocagrande', 'Manga', 'Crespo', 'Getsemaní'], 7_900_000],
        'Pereira' => [['Pinares', 'Álamos', 'Cerritos'], 4_100_000],
    ];

    public function definition(): array
    {
        $ciudad = fake()->randomElement(array_keys(self::CIUDADES));
        [$barrios, $precioM2] = self::CIUDADES[$ciudad];
        $tipo = fake()->randomElement(Propiedad::TIPOS);

        [$tamano, $habitaciones] = match ($tipo) {
            'casa' => [fake()->numberBetween(110, 380), fake()->numberBetween(3, 6)],
            'apartamento' => [fake()->numberBetween(48, 160), fake()->numberBetween(1, 4)],
            'local comercial' => [fake()->numberBetween(30, 250), 0],
        };

        $factor = fake()->randomFloat(2, 0.8, 1.25);
        $precio = (int) (round($tamano * $precioM2 * $factor / 1_000_000) * 1_000_000);

        $via = fake()->randomElement(['Calle', 'Carrera', 'Avenida', 'Diagonal', 'Transversal']);
        $direccion = sprintf('%s %d # %d-%02d, %s', $via, fake()->numberBetween(1, 140), fake()->numberBetween(1, 120), fake()->numberBetween(1, 99), fake()->randomElement($barrios));

        return [
            'direccion' => $direccion,
            'ciudad' => $ciudad,
            'tipo' => $tipo,
            'tamano' => $tamano,
            'numero_habitaciones' => $habitaciones,
            'banos' => $tipo === 'local comercial' ? 1 : max(1, $habitaciones - fake()->numberBetween(0, 1)),
            'precio' => $precio,
            'estado' => 'disponible',
            'descripcion' => null,
        ];
    }
}
