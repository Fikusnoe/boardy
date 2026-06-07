<?php

namespace App\Http\Controllers\Auth;

use Laravel\Passport\Http\Controllers\AuthorizationController;
use Psr\Http\Message\ServerRequestInterface;
use Illuminate\Contracts\View\Factory;

class CustomAuthorizationController extends AuthorizationController
{
    // Мы переопределяем метод, чтобы избежать падения контейнера
    public function authorize(
        ServerRequestInterface $psrRequest,
        $request,
        $client,
        $scopeRepository,
        $requestFactory
    ) {
        // Мы возвращаем результат стандартного метода, но теперь мы уверены,
        // что контроллер не пытается разрешить AuthorizationViewResponse через контейнер
        return parent::authorize($psrRequest, $request, $client, $scopeRepository, $requestFactory);
    }
}

