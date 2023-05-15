<?php

namespace App\Http\Controllers\API;

use App\Services\AgileTeknik\Auth\Data\UpdateUserData;
use App\Services\AgileTeknik\Auth\IAuthService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProfileController extends APIController
{
    private IAuthService $authService;

    public function __construct(IAuthService $authService)
    {
        parent::__construct();
        $this->authService = $authService;
    }

    public function index(): JsonResponse
    {
        $user = $this->authService->getUser();

        return $this->response->resource($user);
    }

    public function update(Request $request): JsonResponse
    {
        $userData = UpdateUserData::from($request);

        $user = $this->authService->updateUser($userData);

        return $this->response->resource($user);
    }
}
