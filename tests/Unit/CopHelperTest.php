<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;

class CopHelperTest extends TestCase
{
    public function test_formatea_pesos_completos(): void
    {
        $this->assertSame('$ 350.000.000', cop(350000000));
        $this->assertSame('$ 0', cop(null));
        $this->assertSame('$ 1.250', cop('1250'));
    }

    public function test_formato_compacto(): void
    {
        $this->assertSame('$ 85.000', cop(85000, true));
        $this->assertSame('$ 350 M', cop(350000000, true));
        $this->assertSame('$ 1,3 mil M', cop(1250000000, true));
    }

    public function test_compacto_no_muestra_mil_millones_como_millones(): void
    {
        $this->assertSame('$ 1,0 mil M', cop(999600000, true));
        $this->assertSame('$ 999 M', cop(999400000, true));
    }
}
