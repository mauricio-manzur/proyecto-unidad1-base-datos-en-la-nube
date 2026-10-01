<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // Invitados que intentan entrar a rutas protegidas -> login
        $middleware->redirectGuestsTo(fn () => route('login'));
        // Usuarios ya autenticados que visitan login/registro -> drive
        $middleware->redirectUsersTo(fn () => route('drive'));
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
