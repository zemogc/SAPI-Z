<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('transacciones', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('propiedad_id');
            $table->foreign('propiedad_id')->references('id')->on('propiedades');

            $table->unsignedBigInteger('cliente_id');
            $table->foreign('cliente_id')->references('id')->on('clientes');

            $table->enum('tipo_transaccion', ['venta', 'compra', 'arrendamiento']);
            $table->date('fecha_transaccion');
            $table->integer('monto_transaccion');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transacciones');
    }
};
