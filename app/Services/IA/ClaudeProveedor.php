<?php

namespace App\Services\IA;

use Anthropic\Beta\Messages\BetaJSONOutputFormat;
use Anthropic\Beta\Messages\BetaMessage;
use Anthropic\Beta\Messages\BetaOutputConfig;
use Anthropic\Client;
use Anthropic\Core\Exceptions\APIConnectionException;
use Anthropic\Core\Exceptions\APIException;
use Anthropic\Core\Exceptions\AuthenticationException;
use Anthropic\Core\Exceptions\BadRequestException;
use Anthropic\Core\Exceptions\RateLimitException;
use Illuminate\Support\Facades\Log;

/**
 * Proveedor de IA basado en Claude, usando el SDK oficial de Anthropic.
 */
class ClaudeProveedor implements ProveedorIA
{
    /** Reintento en el servidor con otro modelo si el solicitado declina la petición. */
    private const BETA_FALLBACK = 'server-side-fallback-2026-07-01';

    public function __construct(
        private ?string $apiKey,
        private string $modelo = 'claude-opus-5-5',
        private string $esfuerzo = 'low',
        private ?Client $cliente = null,
    ) {
    }

    public function disponible(): bool
    {
        return filled($this->apiKey);
    }

    public function texto(string $instrucciones, array $mensajes, string $contexto = '', int $maxTokens = 4000): string
    {
        $sistema = [['type' => 'text', 'text' => $instrucciones]];

        if ($contexto !== '') {
            // El contexto de la cartera cambia poco entre preguntas: se cachea para abaratar el chat.
            $sistema[] = ['type' => 'text', 'text' => $contexto, 'cacheControl' => ['type' => 'ephemeral']];
        }

        return $this->textoDe($this->enviar($sistema, $mensajes, $maxTokens));
    }

    public function json(string $instrucciones, string $prompt, array $esquema, int $maxTokens = 4000): array
    {
        $respuesta = $this->enviar(
            [['type' => 'text', 'text' => $instrucciones]],
            [['role' => 'user', 'content' => $prompt]],
            $maxTokens,
            BetaJSONOutputFormat::with(schema: $esquema),
        );

        $datos = json_decode($this->textoDe($respuesta), true);

        if (! is_array($datos)) {
            throw new ErrorIA('La respuesta del asistente no tuvo el formato esperado. Intenta de nuevo.');
        }

        return $datos;
    }

    private function enviar(array $sistema, array $mensajes, int $maxTokens, ?BetaJSONOutputFormat $formato = null): BetaMessage
    {
        if (! $this->disponible()) {
            throw new ErrorIA('El asistente de IA no está configurado.');
        }

        try {
            $respuesta = $this->cliente()->beta->messages->create(
                maxTokens: $maxTokens,
                messages: $mensajes,
                model: $this->modelo,
                system: $sistema,
                outputConfig: BetaOutputConfig::with(effort: $this->esfuerzo, format: $formato),
                fallbacks: 'default',
                betas: [self::BETA_FALLBACK],
            );
        } catch (AuthenticationException) {
            throw new ErrorIA('La API key de Anthropic no es válida. Revisa ANTHROPIC_API_KEY.');
        } catch (RateLimitException) {
            throw new ErrorIA('El asistente recibió demasiadas solicitudes. Espera un momento e intenta de nuevo.');
        } catch (BadRequestException $e) {
            Log::warning('Solicitud rechazada por la API de Anthropic', ['error' => $e->getMessage()]);
            throw new ErrorIA('La solicitud al asistente no es válida.');
        } catch (APIConnectionException) {
            throw new ErrorIA('No hay conexión con el asistente de IA. Revisa tu red.');
        } catch (APIException $e) {
            Log::error('Error de la API de Anthropic', ['error' => $e->getMessage()]);
            throw new ErrorIA('El asistente no está disponible en este momento.');
        }

        if ($respuesta->stopReason === 'refusal') {
            throw new ErrorIA('El asistente no puede ayudar con esta solicitud.');
        }

        if ($respuesta->stopReason === 'max_tokens') {
            Log::notice('Respuesta del asistente truncada por max_tokens', ['max_tokens' => $maxTokens]);
        }

        return $respuesta;
    }

    private function textoDe(BetaMessage $respuesta): string
    {
        $texto = '';

        foreach ($respuesta->content as $bloque) {
            if ($bloque->type === 'text') {
                $texto .= $bloque->text;
            }
        }

        return $texto;
    }

    private function cliente(): Client
    {
        return $this->cliente ??= new Client(apiKey: $this->apiKey);
    }
}
