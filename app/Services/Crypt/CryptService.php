<?php
/*
 * Copyright (c) 2023 Ahmad Jarir A. - SimHive Group.
 */

namespace App\Services\Crypt;

use Exception;
use Illuminate\Encryption\Encrypter;
use Illuminate\Support\Facades\Log;

class CryptService implements ICryptService
{
    public function encryptString(
        string  $value,
        ?string $key = null,
        string  $cipher = 'aes-256-cbc',
        bool    $throwOnError = false
    ): ?string
    {
        $key = $key ?: $this->getKey();

        try {
            return (new Encrypter($key, $cipher))->encryptString($value);
        } catch (Exception $e) {
            Log::alert('encrypt failed', ['message' => $e->getMessage(), 'trace' => $e->getTrace()]);

            if ($throwOnError) {
                throw $e;
            }
        }

        return null;
    }

    public function decryptString(
        string  $payload,
        ?string $key = null,
        string  $cipher = 'aes-256-cbc',
        bool    $throwOnError = false
    ): ?string
    {
        $key = $key ?: $this->getKey();

        try {
            return (new Encrypter($key, $cipher))->decryptString($payload);
        } catch (Exception $e) {
            Log::alert('encrypt failed', ['message' => $e->getMessage(), 'trace' => $e->getTrace()]);

            if ($throwOnError) {
                throw $e;
            }
        }

        return null;
    }

    public function getKey(): string
    {
        return config('crypt.key');
    }
}
