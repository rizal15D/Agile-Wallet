<?php
/*
 * Copyright (c) 2023 Ahmad Jarir A. - SimHive Group.
 */

namespace App\Services\Crypt;

interface ICryptService
{
    public function encryptString(
        string  $value,
        ?string $key = null,
        string  $cipher = 'aes-256-cbc',
        bool    $throwOnError = false
    ): ?string;

    public function decryptString(
        string  $payload,
        ?string $key = null,
        string  $cipher = 'aes-256-cbc',
        bool    $throwOnError = false
    ): ?string;

    public function getKey(): string;
}
