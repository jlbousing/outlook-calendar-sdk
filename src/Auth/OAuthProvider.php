<?php

namespace OutlookCalendarSDK\Auth;

use GuzzleHttp\Client;
use OutlookCalendarSDK\Exceptions\AuthException;

class OAuthProvider
{
    private $clientId;
    private $clientSecret;
    private $redirectUri;
    private $tenantId;
    private $scopes;

    public function __construct(
        string $clientId,
        string $clientSecret,
        string $redirectUri,
        string $tenantId = 'common',
        array $scopes = ['Calendars.ReadWrite']
    ) {
        $this->clientId = $clientId;
        $this->clientSecret = $clientSecret;
        $this->redirectUri = $redirectUri;
        $this->tenantId = $tenantId;
        $this->scopes = $scopes;
    }

    public function getAuthUrl(): string
    {
        $scopes = implode(' ', $this->scopes);
        $url = "https://login.microsoftonline.com/{$this->tenantId}/oauth2/v2.0/authorize?" . http_build_query([
            'client_id' => $this->clientId,
            'response_type' => 'code',
            'redirect_uri' => $this->redirectUri,
            'response_mode' => 'query',
            'scope' => $scopes,
        ]);

        return $url;
    }

    public function getAccessToken(string $code): array
    {
        $client = new Client();

        try {
            $response = $client->post("https://login.microsoftonline.com/{$this->tenantId}/oauth2/v2.0/token", [
                'form_params' => [
                    'client_id' => $this->clientId,
                    'client_secret' => $this->clientSecret,
                    'code' => $code,
                    'redirect_uri' => $this->redirectUri,
                    'grant_type' => 'authorization_code',
                    'scope' => implode(' ', $this->scopes),
                ],
            ]);

            return json_decode($response->getBody(), true);
        } catch (\Exception $e) {
            throw new AuthException('Failed to get access token: ' . $e->getMessage());
        }
    }

    public function refreshToken(string $refreshToken): array
    {
        $client = new Client();

        try {
            $response = $client->post("https://login.microsoftonline.com/{$this->tenantId}/oauth2/v2.0/token", [
                'form_params' => [
                    'client_id' => $this->clientId,
                    'client_secret' => $this->clientSecret,
                    'refresh_token' => $refreshToken,
                    'grant_type' => 'refresh_token',
                    'scope' => implode(' ', $this->scopes),
                ],
            ]);

            return json_decode($response->getBody(), true);
        } catch (\Exception $e) {
            throw new AuthException('Failed to refresh token: ' . $e->getMessage());
        }
    }
}
