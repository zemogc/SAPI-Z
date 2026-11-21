<?php

if (! function_exists('cop')) {
    /**
     * Formatea un valor como pesos colombianos: 350000000 => "$ 350.000.000".
     */
    function cop(float|int|string|null $valor, bool $compacto = false): string
    {
        $valor = (float) $valor;

        if ($compacto) {
            return match (true) {
                // Desde 999.500.000 los millones redondeados ya serian "1.000 M"
                $valor >= 999_500_000 => '$ '.number_format($valor / 1_000_000_000, 1, ',', '.').' mil M',
                $valor >= 1_000_000 => '$ '.number_format($valor / 1_000_000, 0, ',', '.').' M',
                default => '$ '.number_format($valor, 0, ',', '.'),
            };
        }

        return '$ '.number_format($valor, 0, ',', '.');
    }
}
