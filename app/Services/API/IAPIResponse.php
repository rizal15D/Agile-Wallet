<?php
/*
 * Copyright (c) 2023 Ahmad Jarir A. - SimHive Group.
 */

namespace App\Services\API;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Support\Collection;

interface IAPIResponse
{
    public function direct(mixed $data, int $status = 200): JsonResponse;

    public function message(string $message = '', int $status = 200): JsonResponse;

    public function error(string $errorMessage, int $status): JsonResponse;

    public function resource(mixed $resource, string $resourceClass = JsonResource::class): JsonResponse;

    public function resourceCollection(
        Collection $collection,
        string     $resourceClass = ResourceCollection::class
    ): JsonResponse;
}
