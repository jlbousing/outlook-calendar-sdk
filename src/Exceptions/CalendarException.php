<?php

namespace OutlookCalendarSDK\Exceptions;

/**
 * Excepción lanzada cuando ocurren errores al interactuar con la API de Calendar
 */
class CalendarException extends \RuntimeException
{
    /**
     * @var array|null Respuesta completa del error de la API
     */
    private ?array $apiResponse;

    /**
     * @var int|null Código de estado HTTP
     */
    private ?int $statusCode;

    public function __construct(
        string $message,
        ?array $apiResponse = null,
        ?int $statusCode = null,
        int $code = 0,
        \Throwable $previous = null
    ) {
        parent::__construct($message, $code, $previous);
        $this->apiResponse = $apiResponse;
        $this->statusCode = $statusCode;
    }

    /**
     * Obtiene la respuesta completa de la API
     */
    public function getApiResponse(): ?array
    {
        return $this->apiResponse;
    }

    /**
     * Obtiene el código de estado HTTP
     */
    public function getStatusCode(): ?int
    {
        return $this->statusCode;
    }

    /**
     * Crea una CalendarException a partir de una respuesta de error de la API
     */
    public static function fromApiResponse(array $response, int $statusCode): self
    {
        $error = $response['error'] ?? [];
        $code = $error['code'] ?? 'UnknownError';
        $message = $error['message'] ?? 'No error message provided';

        return new self(
            sprintf('Calendar API error (%s): %s', $code, $message),
            $response,
            $statusCode
        );
    }
}
