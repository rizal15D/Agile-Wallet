<?php
/*
 * Copyright (c) 2023 Ahmad Jarir A. - SimHive Group.
 */

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Services\API\APIResponse;
use App\Services\API\IAPIResponse;

abstract class APIController extends Controller
{
    protected IAPIResponse $response;

    public function __construct()
    {
        $this->response = app(APIResponse::class);
    }
}
