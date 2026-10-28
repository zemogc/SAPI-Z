<?php

namespace Tests\Unit;

use Anthropic\Client;
use App\Services\IA\ClaudeProveedor;
use App\Services\IA\ErrorIA;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

/**
 * Verifica la petición que se envía a la API de Claude sin hacer llamadas reales.
 */
class ClaudeProveedorTest extends TestCase
{
    private TransporteFalso $transporte;

    private function proveedor(array $respuesta, int $estado = 200): ClaudeProveedor
    {
        $this->transporte = new TransporteFalso(new Response($estado, ['Content-Type' => 'application/json'], json_encode($respuesta)));
        $cliente = new Client(apiKey: 'clave-de-prueba', requestOptions: ['transporter' => $this->transporte, 'maxRetries' => 0]);

        return new ClaudeProveedor('clave-de-prueba', 'claude-opus-5-5', 'low', $cliente);
    }

    private function mensaje(string $texto, string $stopReason = 'end_turn'): array
    {
        return [
            'id' => 'msg_1', 'type' => 'message', 'role' => 'assistant', 'model' => 'claude-opus-5-5',
            'content' => [['type' => 'thinking', 'thinking' => '', 'signature' => 'x'], ['type' => 'text', 'text' => $texto]],
            'stop_reason' => $stopReason, 'stop_sequence' => null, 'stop_details' => null,
            'usage' => ['input_tokens' => 10, 'output_tokens' => 5],
        ];
    }

    public function test_envia_modelo_esfuerzo_fallbacks_y_contexto_cacheado(): void
    {
        $texto = $this->proveedor($this->mensaje('Hola'))
            ->texto('Instrucciones', [['role' => 'user', 'content' => '¿Cuántas casas hay?']], 'DATOS DE LA CARTERA');

        $this->assertSame('Hola', $texto);

        $peticion = $this->transporte->peticion;
        $cuerpo = json_decode((string) $peticion->getBody(), true);

        $this->assertStringEndsWith('/v1/messages', $peticion->getUri()->getPath());
        $this->assertStringContainsString('server-side-fallback-2026-07-01', $peticion->getHeaderLine('anthropic-beta'));
        $this->assertSame('claude-opus-5-5', $cuerpo['model']);
        $this->assertSame('default', $cuerpo['fallbacks']);
        $this->assertSame('low', $cuerpo['output_config']['effort']);
        $this->assertSame('DATOS DE LA CARTERA', $cuerpo['system'][1]['text']);
        $this->assertSame(['type' => 'ephemeral'], $cuerpo['system'][1]['cache_control']);
        $this->assertArrayNotHasKey('thinking', $cuerpo);
    }

    public function test_pide_salida_estructurada_y_la_decodifica(): void
    {
        $esquema = ['type' => 'object', 'properties' => ['sugerido' => ['type' => 'integer']], 'required' => ['sugerido'], 'additionalProperties' => false];

        $datos = $this->proveedor($this->mensaje('{"sugerido": 450000000}'))->json('Instrucciones', 'Valora esto', $esquema);

        $this->assertSame(['sugerido' => 450000000], $datos);
        $cuerpo = json_decode((string) $this->transporte->peticion->getBody(), true);
        $this->assertSame(['type' => 'json_schema', 'schema' => $esquema], $cuerpo['output_config']['format']);
    }

    public function test_convierte_un_rechazo_en_error_legible(): void
    {
        $this->expectException(ErrorIA::class);

        $this->proveedor($this->mensaje('', 'refusal'))->texto('x', [['role' => 'user', 'content' => 'y']]);
    }

    public function test_traduce_errores_de_autenticacion(): void
    {
        $this->expectExceptionObject(new ErrorIA('La API key de Anthropic no es válida. Revisa ANTHROPIC_API_KEY.'));

        $this->proveedor(['type' => 'error', 'error' => ['type' => 'authentication_error', 'message' => 'invalid x-api-key']], 401)
            ->texto('x', [['role' => 'user', 'content' => 'y']]);
    }

    public function test_no_esta_disponible_sin_api_key(): void
    {
        $proveedor = new ClaudeProveedor(null);

        $this->assertFalse($proveedor->disponible());
        $this->expectException(ErrorIA::class);
        $proveedor->texto('x', [['role' => 'user', 'content' => 'y']]);
    }
}

class TransporteFalso implements ClientInterface
{
    public ?RequestInterface $peticion = null;

    public function __construct(private ResponseInterface $respuesta)
    {
    }

    public function sendRequest(RequestInterface $request): ResponseInterface
    {
        $this->peticion = $request;

        return $this->respuesta;
    }
}
