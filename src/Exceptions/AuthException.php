<?php

namespace OutlookCalendarSDK\Exceptions;

/**
 * Excepción lanzada cuando ocurren errores durante el proceso de autenticación OAuth
 */
class AuthException extends \RuntimeException
{
    /**
     * @var array|null Detalles adicionales del error de autenticación
     */
    private ?array $errorDetails;

    public function __construct(string $message, ?array $errorDetails = null, int $code = 0, \Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
        $this->errorDetails = $errorDetails;
    }

    /**
     * Obtiene los detalles del error de autenticación
     */
    public function getErrorDetails(): ?array
    {
        return $this->errorDetails;
    }

    /**
     * Crea una AuthException a partir de una respuesta de error de OAuth
     */
    public static function fromOAuthError(array $errorResponse): self
    {
        $error = $errorResponse['error'] ?? 'unknown_error';
        $description = $errorResponse['error_description'] ?? 'No error description provided';

        return new self(
            sprintf('OAuth error: %s - %s', $error, $description),
            $errorResponse
        );
    }
}
