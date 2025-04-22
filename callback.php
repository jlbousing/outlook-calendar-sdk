<?php

/**
 * Archivo de callback para la autenticación OAuth de Microsoft
 * 
 * Este archivo recibe el código de autorización de Microsoft y lo procesa
 * para obtener el token de acceso.
 */

// Incluimos el autoloader de Composer
require_once __DIR__ . '/vendor/autoload.php';

// Importamos las clases necesarias
use OutlookCalendarSDK\Auth\OAuthProvider;
use OutlookCalendarSDK\Exceptions\AuthException;

// Configuración de la aplicación (debe coincidir con la de ejemplo.php)
$clientId = 'TU_CLIENT_ID'; // Reemplazar con tu Client ID de Microsoft
$clientSecret = 'TU_CLIENT_SECRET'; // Reemplazar con tu Client Secret de Microsoft
$redirectUri = 'http://localhost/callback.php'; // URL de redirección configurada en tu aplicación de Microsoft
$scopes = ['Calendars.ReadWrite'];

// Verificar si recibimos un código de autorización
if (!isset($_GET['code'])) {
    die('No se recibió el código de autorización.');
}

// Inicializar el proveedor de OAuth
$oauthProvider = new OAuthProvider($clientId, $clientSecret, $redirectUri, 'common', $scopes);

try {
    // Obtener el token de acceso
    $tokenData = $oauthProvider->getAccessToken($_GET['code']);

    // Guardar los tokens (en una aplicación real, deberías guardarlos de forma segura)
    $accessToken = $tokenData['access_token'];
    $refreshToken = $tokenData['refresh_token'];
    $expiresIn = $tokenData['expires_in'];

    // En una aplicación real, deberías guardar estos tokens en una base de datos
    // o en una sesión segura para usarlos posteriormente

    // Por ahora, solo mostraremos un mensaje de éxito
    echo "Autenticación exitosa!<br>";
    echo "Token de acceso obtenido correctamente.<br>";
    echo "El token expira en: " . $expiresIn . " segundos.<br>";
    echo "<a href='ejemplo.php'>Volver al ejemplo</a>";
} catch (AuthException $e) {
    echo "Error de autenticación: " . $e->getMessage();
}
