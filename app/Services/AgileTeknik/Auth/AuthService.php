<?php
/*
 * Copyright (c) 2023 Ahmad Jarir A. - SimHive Group.
 */

namespace App\Services\AgileTeknik\Auth;

use App\Models\User;
use App\Services\AgileTeknik\Auth\Data\LoginData;
use App\Services\AgileTeknik\Auth\Data\RegisterData;
use App\Services\AgileTeknik\Auth\Data\UpdateUserData;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response as SymfonyResponse;

class AuthService implements IAuthService
{
    private AuthSettings $authSetting;

    public function __construct(AuthSettings $authSetting)
    {
        $this->authSetting = $authSetting;
    }

    public function login(LoginData $loginData): User
    {
        $endpoint = $this->getUrl('api/v1/login');

        $response = $this->makeRequestWithClientToken($endpoint)->post($endpoint, $loginData->toArray());

        $this->ensureRequestSucceed($response);

        return $this->updateOrCreateUserFromResponse($response);
    }

    public function register(RegisterData $registerData): User
    {
        $endpoint = $this->getUrl('api/v1/register');
        $response = $this->makeRequestWithClientToken($endpoint)->post($endpoint, $registerData->toArray());

        $this->ensureRequestSucceed($response);

        return $this->updateOrCreateUserFromResponse($response);
    }

    public function getUser(): User
    {
        $endpoint = $this->getUrl('api/v1/user');

        $response = $this->makeRequestWithUserToken($endpoint)->get($endpoint);

        $this->ensureRequestSucceed($response);

        return $this->updateOrCreateUserFromResponse($response);
    }

    public function updateUser(UpdateUserData $userData): User
    {
        $endpoint = $this->getUrl('api/v1/user');

        $response = $this->makeRequestWithUserToken($endpoint)->patch($endpoint, $userData->toArray());

        $this->ensureRequestSucceed($response);

        return $this->updateOrCreateUserFromResponse($response);
    }

    private function updateOrCreateUserFromResponse(Response $response): User
    {
        return User::updateOrCreate([
            'email' => $response->json('data.email'),
        ], [
            'name' => $response->json('data.name'),
            'agileteknik_access_token' => $response->json('data.access_token'),
        ]);
    }

    public function makeRequestWithUserToken(bool $asJson = true): PendingRequest
    {
        $accessToken = auth()->user()->agileteknik_access_token;

        return $this->createPendingRequest($asJson, $accessToken);
    }

    public function makeRequestWithClientToken(bool $asJson = true): PendingRequest
    {
        $accessToken = $this->getOauthClientAccessToken();

        return $this->createPendingRequest($asJson, $accessToken);
    }

    public function getOauthClientAccessToken(bool $forceUpdate = false): string
    {
        $accessToken = $this->authSetting->clientAccessToken;
        if (! empty($accessToken) && ! $forceUpdate) {
            return $accessToken;
        }

        $endpoint = $this->getUrl('oauth/token');

        $response = Http::asForm()->acceptJson()->post($endpoint, [
            'grant_type' => 'client_credentials',
            'client_id' => config('at_auth.client_id'),
            'client_secret' => config('at_auth.client_secret'),
            'scope' => 'access-client-features',
        ]);

        $this->ensureRequestSucceed($response);

        $accessToken = $response->json()['access_token'];
        $this->authSetting->clientAccessToken = $accessToken;
        $this->authSetting->save();

        return $accessToken;
    }

    public function ensureRequestSucceed(Response $response): void
    {
        if ($response->status() === SymfonyResponse::HTTP_UNPROCESSABLE_ENTITY) {
            throw ValidationException::withMessages($response->json('errors'));
        }

        if (! $response->successful()) {
            Log::error('Request failed. Status: '.$response->status(), $response->json());
            abort($response->status());
        }
    }

    private function createPendingRequest(bool $asJson, $accessToken): PendingRequest
    {
        return Http::acceptJson()->when($asJson, function (PendingRequest $request) {
            $request->asJson();
        })->withToken($accessToken);
    }

    public function getUrl(string $endpoint): string
    {
        $baseUrl = rtrim($this->getBaseUrl(), '/');
        $endpoint = ltrim($endpoint, '/');

        return $baseUrl.'/'.$endpoint;
    }

    public function getBaseUrl(): string
    {
        return config('at_auth.base_url');
    }
}
