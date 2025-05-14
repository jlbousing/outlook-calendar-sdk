# SDK de Outlook Calendar para PHP

Este paquete proporciona una integración sencilla con la API de Microsoft Outlook Calendar para aplicaciones PHP. Permite gestionar eventos, reuniones y citas en calendarios de Outlook/Office 365, incluyendo soporte completo para reuniones de Microsoft Teams.

## Requisitos

- PHP 7.4 o superior
- Extensión PHP JSON
- Cuenta de Microsoft y una aplicación registrada en Azure Portal
- Permisos necesarios para reuniones en línea (Teams)

## Instalación

Instala el paquete usando Composer:

```bash
composer require jlbousing/outlook-calendar-sdk
```

## Configuración

Para usar el SDK, necesitarás registrar una aplicación en el [Portal de Azure](https://portal.azure.com) y obtener un Client ID y Client Secret:

1. Inicia sesión en el [Portal de Azure](https://portal.azure.com)
2. Navega a "Azure Active Directory" > "Registros de aplicaciones"
3. Crea una nueva aplicación
4. Configura los permisos necesarios:
   - `Calendars.ReadWrite` (para gestión básica del calendario)
   - `OnlineMeetings.ReadWrite` (para reuniones de Teams)
5. Añade una URL de redirección (por ejemplo, `http://localhost/callback.php`)
6. Crea un secreto de cliente

## Ejemplo básico de uso

```php
<?php
require_once __DIR__ . '/vendor/autoload.php';

use OutlookCalendarSDK\Auth\OAuthProvider;
use OutlookCalendarSDK\Services\EventService;
use OutlookCalendarSDK\Models\Event;

// Configuración
$clientId = 'TU_CLIENT_ID';
$clientSecret = 'TU_CLIENT_SECRET';
$redirectUri = 'http://localhost/callback.php';

// Autenticación
$oauthProvider = new OAuthProvider($clientId, $clientSecret, $redirectUri);

// Obtener URL de autorización
$authUrl = $oauthProvider->getAuthUrl();

// Tras la redirección, obtener el token
$tokenData = $oauthProvider->getAccessToken($_GET['code']);
$accessToken = $tokenData['access_token'];

// Crear un servicio de eventos
$eventService = new EventService($accessToken);

// Crear un nuevo evento con Teams
$startTime = new DateTime();
$startTime->modify('+1 day');
$endTime = clone $startTime;
$endTime->modify('+1 hour');

$event = new Event(
    'Reunión de equipo',
    $startTime,
    $endTime,
    '<p>Discutiremos los avances del proyecto.</p>',
    'Microsoft Teams'
);

// Habilitar la reunión de Teams
$event->setOnlineMeeting(true);

// Publicar el evento en el calendario
$createdEvent = $eventService->createEvent($event);

// Obtener la URL de la reunión de Teams
$teamsUrl = $event->getOnlineMeetingJoinUrl();
```

## Funcionalidades principales

### Autenticación

El SDK utiliza OAuth 2.0 para autenticarse con Microsoft Graph API:

```php
$oauthProvider = new OAuthProvider($clientId, $clientSecret, $redirectUri);
$authUrl = $oauthProvider->getAuthUrl(); // URL para dirigir al usuario
$tokenData = $oauthProvider->getAccessToken($code); // Obtener token con el código
$newTokenData = $oauthProvider->refreshToken($refreshToken); // Renovar token
```

### Gestión de eventos

```php
// Crear evento
$event = new Event($subject, $startDateTime, $endDateTime, $body, $location);
$eventService->createEvent($event);

// Listar eventos
$events = $eventService->listEvents(['$top' => 10]);

// Obtener un evento específico
$event = $eventService->getEvent($eventId);

// Actualizar evento
$eventService->updateEvent($eventId, $updatedEvent);

// Eliminar evento
$eventService->deleteEvent($eventId);
```

### Añadir participantes

```php
$event = new Event('Reunión', $start, $end);
$event->addAttendee(new Attendee('usuario@ejemplo.com', 'Nombre Usuario', 'required'));
```

### Configurar reuniones online (Teams)

```php
// Crear un evento con Teams
$event = new Event('Reunión virtual', $start, $end);
$event->setOnlineMeeting(true); // Habilitar Teams

// Crear el evento
$createdEvent = $eventService->createEvent($event);

// Obtener la URL de la reunión
$joinUrl = $event->getOnlineMeetingJoinUrl();

// Configurar opciones adicionales
$event->setAllowNewTimeProposals(true); // Permitir proponer nuevos horarios
```

### Características de reuniones Teams

- Generación automática de enlaces de reunión
- Integración con el calendario de Outlook
- Soporte para propuestas de nuevos horarios
- URLs únicas para unirse a la reunión
- Notificaciones automáticas por correo a los participantes

## Manejo de respuestas

El SDK maneja automáticamente las respuestas de la API y actualiza los objetos Event con la información relevante:

```php
// La URL de Teams se establece automáticamente después de crear el evento
$event = new Event('Reunión virtual', $start, $end);
$event->setOnlineMeeting(true);
$eventService->createEvent($event);

// Obtener la URL de Teams
$teamsUrl = $event->getOnlineMeetingJoinUrl();
```

## Documentación de la API de Microsoft Graph

Para más información sobre la API de Microsoft Graph, consulta la [documentación oficial](https://docs.microsoft.com/en-us/graph/api/resources/calendar?view=graph-rest-1.0).

## Licencia

Este proyecto está licenciado bajo la Licencia MIT.
