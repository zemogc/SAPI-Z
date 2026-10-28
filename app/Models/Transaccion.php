<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Transaccion extends Model
{
    use HasFactory;

    public const TIPOS = ['venta', 'compra', 'arrendamiento'];

    protected $table = 'transacciones';

    protected $fillable = [
        'propiedad_id',
        'cliente_id',
        'tipo_transaccion',
        'fecha_transaccion',
        'monto_transaccion',
        'notas',
    ];

    protected function casts(): array
    {
        return [
            'fecha_transaccion' => 'date',
            'monto_transaccion' => 'decimal:2',
        ];
    }

    /**
     * Mantiene el estado de la propiedad sincronizado con su última transacción:
     * una venta o compra la marca como vendida y un arrendamiento como arrendada.
     */
    protected static function booted(): void
    {
        static::saved(function (Transaccion $transaccion) {
            if ($transaccion->wasChanged('propiedad_id') && $transaccion->getOriginal('propiedad_id')) {
                static::sincronizarEstado(Propiedad::find($transaccion->getOriginal('propiedad_id')));
            }

            static::sincronizarEstado($transaccion->propiedad);
        });

        static::deleted(function (Transaccion $transaccion) {
            static::sincronizarEstado($transaccion->propiedad);
        });
    }

    public static function sincronizarEstado(?Propiedad $propiedad): void
    {
        if (! $propiedad) {
            return;
        }

        $ultima = $propiedad->transacciones()
            ->orderByDesc('fecha_transaccion')
            ->orderByDesc('id')
            ->first();

        $estado = match ($ultima?->tipo_transaccion) {
            'venta', 'compra' => 'vendida',
            'arrendamiento' => 'arrendada',
            default => 'disponible',
        };

        if ($propiedad->estado !== $estado) {
            $propiedad->update(['estado' => $estado]);
        }
    }

    public function propiedad(): BelongsTo
    {
        return $this->belongsTo(Propiedad::class);
    }

    public function cliente(): BelongsTo
    {
        return $this->belongsTo(Cliente::class);
    }
}
