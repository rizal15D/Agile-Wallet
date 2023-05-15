<?php
/*
 * Copyright (c) 2023 Ahmad Jarir A. - SimHive Group.
 */

namespace App\Services\API;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Support\Collection;

class APIResponse implements IAPIResponse
{
    public function direct(mixed $data, int $status = 200): JsonResponse
    {
        return response()->json($data, $status);
    }

    public function message(string $message = '', int $status = 200): JsonResponse
    {
        return response()->json(['message' => $message], $status);
    }

    public function error(string $errorMessage, int $status): JsonResponse
    {
        return response()->json(['error' => $errorMessage], $status);
    }

    public function resource(mixed $resource, string $resourceClass = JsonResource::class): JsonResponse
    {
        if (!is_a($resourceClass, JsonResource::class, true)) {
            throw new APIResponseException('Resource class must extends JsonResource class');
        }

        return $resourceClass::make($resource)->response();
    }

    public function resourceCollection(
        Collection $collection,
        string     $resourceClass = ResourceCollection::class
    ): JsonResponse
    {
        if (!is_a($resourceClass, ResourceCollection::class, true)) {
            throw new APIResponseException('Resource class must extends ResourceCollection class');
        }

        return $resourceClass::make($collection)->response();
    }

}
