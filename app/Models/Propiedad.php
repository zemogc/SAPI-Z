<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Propiedad extends Model
{
    use HasFactory;

    public const TIPOS = ['casa', 'apartamento', 'local comercial'];

    public const ESTADOS = ['disponible', 'vendida', 'arrendada'];

    /** Sugerencias para el campo ciudad. */
    public const CIUDADES = ['Cali', 'Bogotá', 'Medellín', 'Barranquilla', 'Cartagena', 'Pereira', 'Bucaramanga', 'Manizales'];

    protected $table = 'propiedades';

    protected $fillable = [
        'direccion',
        'ciudad',
        'tipo',
        'tamano',
        'numero_habitaciones',
        'banos',
        'precio',
        'estado',
        'descripcion',
    ];

    protected function casts(): array
    {
        return [
            'tamano' => 'decimal:2',
            'precio' => 'decimal:2',
            'numero_habitaciones' => 'integer',
            'banos' => 'integer',
        ];
    }

    public function transacciones(): HasMany
    {
        return $this->hasMany(Transaccion::class);
    }

    /**
     * Precio por metro cuadrado, útil para comparar inmuebles de distinto tamaño.
     */
    public function getPrecioM2Attribute(): ?float
    {
        return (float) $this->tamano > 0 ? round((float) $this->precio / (float) $this->tamano, 2) : null;
    }

    public function scopeBuscar(Builder $query, ?string $texto): Builder
    {
        if (blank($texto)) {
            return $query;
        }

        return $query->where(function (Builder $q) use ($texto) {
            $q->where('direccion', 'like', "%{$texto}%")
                ->orWhere('ciudad', 'like', "%{$texto}%")
                ->orWhere('descripcion', 'like', "%{$texto}%");
        });
    }

    public function scopeDisponibles(Builder $query): Builder
    {
        return $query->where('estado', 'disponible');
    }
}
