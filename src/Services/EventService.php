<?php

namespace OutlookCalendarSDK\Services;

use GuzzleHttp\Client;
use OutlookCalendarSDK\Models\Event;
use OutlookCalendarSDK\Exceptions\CalendarException;

class EventService
{
    private $client;
    private $accessToken;

    public function __construct(string $accessToken)
    {
        $this->accessToken = $accessToken;
        $this->client = new Client([
            'base_uri' => 'https://graph.microsoft.com/v1.0/',
            'headers' => [
                'Authorization' => 'Bearer ' . $this->accessToken,
                'Content-Type' => 'application/json',
                'Prefer' => 'outlook.timezone="UTC"'
            ]
        ]);
    }

    public function createEvent(Event $event): array
    {
        try {
            $response = $this->client->post('me/events', [
                'json' => $event->toArray()
            ]);

            return json_decode($response->getBody(), true);
        } catch (\Exception $e) {
            throw new CalendarException('Failed to create event: ' . $e->getMessage());
        }
    }

    public function updateEvent(string $eventId, Event $event): array
    {
        try {
            $response = $this->client->patch("me/events/{$eventId}", [
                'json' => $event->toArray()
            ]);

            return json_decode($response->getBody(), true);
        } catch (\Exception $e) {
            throw new CalendarException('Failed to update event: ' . $e->getMessage());
        }
    }

    public function deleteEvent(string $eventId): bool
    {
        try {
            $response = $this->client->delete("me/events/{$eventId}");
            return $response->getStatusCode() === 204;
        } catch (\Exception $e) {
            throw new CalendarException('Failed to delete event: ' . $e->getMessage());
        }
    }

    public function getEvent(string $eventId): array
    {
        try {
            $response = $this->client->get("me/events/{$eventId}");
            return json_decode($response->getBody(), true);
        } catch (\Exception $e) {
            throw new CalendarException('Failed to get event: ' . $e->getMessage());
        }
    }

    public function listEvents(array $params = []): array
    {
        try {
            $query = !empty($params) ? ['query' => $params] : [];
            $response = $this->client->get('me/events', $query);
            return json_decode($response->getBody(), true);
        } catch (\Exception $e) {
            throw new CalendarException('Failed to list events: ' . $e->getMessage());
        }
    }
}
