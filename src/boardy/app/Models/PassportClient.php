<?php

namespace App\Models;

use Laravel\Passport\Client;
use Illuminate\Contracts\Auth\Authenticatable;

class PassportClient extends Client
{
    /**
     * Всегда пропускать экран "Разрешить приложению доступ"
     */
    public function skipsAuthorization(Authenticatable $user, array $scopes): bool
    {
        return true;
    }
}
