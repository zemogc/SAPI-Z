<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Convierte precios, montos y tamaños (antes texto libre como "100.000.000")
 * a columnas numéricas y agrega los campos que usa el asistente y el dashboard.
 */
return new class extends Migration
{
    public function up(): void
    {
        $this->normalizarNumeros('propiedades', ['precio']);
        $this->normalizarNumeros('propiedades', ['tamano'], area: true);
        $this->normalizarNumeros('transacciones', ['monto_transaccion']);

        DB::table('propiedades')->update(['tipo' => DB::raw('LOWER(tipo)')]);

        Schema::table('propiedades', function (Blueprint $table) {
            $table->decimal('precio', 15, 2)->change();
            $table->decimal('tamano', 10, 2)->change();
            $table->string('ciudad')->nullable()->after('direccion');
            $table->unsignedTinyInteger('banos')->nullable()->after('numero_habitaciones');
            $table->text('descripcion')->nullable()->after('estado');
            $table->index('estado');
            $table->index('tipo');
        });

        Schema::table('transacciones', function (Blueprint $table) {
            $table->decimal('monto_transaccion', 15, 2)->change();
            $table->text('notas')->nullable()->after('monto_transaccion');
            $table->index('fecha_transaccion');
        });

        Schema::table('clientes', function (Blueprint $table) {
            $table->index('email');
        });
    }

    public function down(): void
    {
        Schema::table('clientes', function (Blueprint $table) {
            $table->dropIndex(['email']);
        });

        Schema::table('transacciones', function (Blueprint $table) {
            $table->dropIndex(['fecha_transaccion']);
            $table->dropColumn('notas');
            $table->string('monto_transaccion')->change();
        });

        Schema::table('propiedades', function (Blueprint $table) {
            $table->dropIndex(['estado']);
            $table->dropIndex(['tipo']);
            $table->dropColumn(['ciudad', 'banos', 'descripcion']);
            $table->string('precio')->change();
            $table->string('tamano')->change();
        });
    }

    /**
     * Montos: deja solo dígitos (los puntos se usaban como separador de miles).
     * Áreas: toma el primer número ("120 m2" => 120, "85,5 m²" => 85.5).
     */
    private function normalizarNumeros(string $tabla, array $columnas, bool $area = false): void
    {
        DB::table($tabla)->orderBy('id')->each(function ($fila) use ($tabla, $columnas, $area) {
            $valores = [];
            foreach ($columnas as $columna) {
                $texto = (string) $fila->{$columna};

                if ($area) {
                    $texto = preg_replace('/\.(?=\d{3}(?:\D|$))/', '', $texto); // "1.200 m2" => "1200 m2"
                    preg_match('/\d+(?:[.,]\d{1,2}(?!\d))?/', $texto, $coincidencia);
                    $limpio = str_replace(',', '.', $coincidencia[0] ?? '');
                } else {
                    $limpio = preg_replace('/[^0-9]/', '', $texto);
                }

                $valores[$columna] = $limpio === '' ? 0 : $limpio;
            }
            DB::table($tabla)->where('id', $fila->id)->update($valores);
        });
    }
};
