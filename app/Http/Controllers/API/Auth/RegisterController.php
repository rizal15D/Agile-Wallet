<?php
/*
 * Copyright (c) 2023 Ahmad Jarir A. - SimHive Group.
 */

namespace App\Http\Controllers\API\Auth;

use App\Http\Controllers\API\APIController;
use App\Services\AgileTeknik\Auth\Data\RegisterData;
use App\Services\AgileTeknik\Auth\IAuthService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class RegisterController extends APIController
{
    private IAuthService $authService;

    public function __construct(IAuthService $authService)
    {
        parent::__construct();
        $this->authService = $authService;
    }

    public function store(Request $request): JsonResponse
    {
        $registerData = RegisterData::withoutMagicalCreationFrom($request);
        $user = $this->authService->register($registerData);

        $accessToken = $user->createToken($user->email)->accessToken;

        return $this->response->resource([
            ...$user->toArray(),
            'access_token' => $accessToken,
        ]);
    }
}
