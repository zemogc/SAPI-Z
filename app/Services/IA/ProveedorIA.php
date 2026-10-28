<?php

namespace App\Services\IA;

/**
 * Contrato mínimo que el asistente necesita de un modelo de lenguaje.
 * Permite cambiar de proveedor o usar uno falso en las pruebas.
 */
interface ProveedorIA
{
    /** Indica si el proveedor está configurado (por ejemplo, si hay API key). */
    public function disponible(): bool;

    /**
     * Devuelve la respuesta en texto libre.
     *
     * @param  string  $contexto  Bloque estable (datos de la cartera) que se puede cachear.
     * @param  array<int, array{role: string, content: string}>  $mensajes
     */
    public function texto(string $instrucciones, array $mensajes, string $contexto = '', int $maxTokens = 4000): string;

    /**
     * Devuelve un objeto que cumple el JSON Schema indicado.
     *
     * @param  array<string, mixed>  $esquema
     * @return array<string, mixed>
     */
    public function json(string $instrucciones, string $prompt, array $esquema, int $maxTokens = 4000): array;
}
