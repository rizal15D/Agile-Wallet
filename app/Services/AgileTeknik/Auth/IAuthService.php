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

interface IAuthService
{
    public function login(LoginData $loginData): User;

    public function register(RegisterData $registerData): User;

    public function getUser(): User;

    public function updateUser(UpdateUserData $userData): User;

    public function makeRequestWithClientToken(bool $asJson = true);

    public function makeRequestWithUserToken(bool $asJson = true): PendingRequest;

    public function getOauthClientAccessToken(bool $forceUpdate = false): string;

    public function ensureRequestSucceed(Response $response): void;

    public function getUrl(string $endpoint): string;

    public function getBaseUrl(): string;
}
