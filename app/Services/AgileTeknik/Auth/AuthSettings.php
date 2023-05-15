<?php
/*
 * Copyright (c) 2023 Ahmad Jarir A. - SimHive Group.
 */

namespace App\Services\AgileTeknik\Auth;

use Spatie\LaravelSettings\Settings;

class AuthSettings extends Settings
{
    public ?string $clientAccessToken = null;

    public static function group(): string
    {
        return 'agileteknik';
    }
}
