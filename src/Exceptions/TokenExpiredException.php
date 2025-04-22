<?php

namespace OutlookCalendarSDK\Exceptions;

/**
 * Excepción específica para cuando el token de acceso ha expirado
 */
class TokenExpiredException extends AuthException
{
    public function __construct(string $message = 'Access token has expired', int $code = 0, \Throwable $previous = null)
    {
        parent::__construct($message, null, $code, $previous);
    }
}
