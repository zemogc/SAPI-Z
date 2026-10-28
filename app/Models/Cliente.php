<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Cliente extends Model
{
    use HasFactory;

    public const TIPOS = ['comprador', 'vendedor', 'arrendatario'];

    protected $table = 'clientes';

    protected $fillable = ['nombre', 'apellido', 'telefono', 'email', 'tipo'];

    public function transacciones(): HasMany
    {
        return $this->hasMany(Transaccion::class);
    }

    public function getNombreCompletoAttribute(): string
    {
        return trim("{$this->nombre} {$this->apellido}");
    }

    public function getInicialesAttribute(): string
    {
        return mb_strtoupper(mb_substr($this->nombre, 0, 1).mb_substr($this->apellido, 0, 1));
    }

    public function scopeBuscar(Builder $query, ?string $texto): Builder
    {
        if (blank($texto)) {
            return $query;
        }

        return $query->where(function (Builder $q) use ($texto) {
            $q->where('nombre', 'like', "%{$texto}%")
                ->orWhere('apellido', 'like', "%{$texto}%")
                ->orWhere('email', 'like', "%{$texto}%")
                ->orWhere('telefono', 'like', "%{$texto}%");
        });
    }
}
