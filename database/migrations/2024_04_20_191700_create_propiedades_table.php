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
        Schema::create('propiedades', function (Blueprint $table) {
            $table->id();

            $table->string('direccion');
            $table->enum('tipo', ['casa', 'apartamento', 'local comercial']);
            $table->string('tamano');
            $table->integer('numero_habitaciones');
            $table->string('precio');
            $table->enum('estado', ['disponible', 'vendida', 'arrendada']);
    
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('propiedades');
    }
};
