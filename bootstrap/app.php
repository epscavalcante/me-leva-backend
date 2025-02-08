<?php

use Core\Domain\Exceptions\AccountAlreadExistsException;
use Core\Domain\Exceptions\NotFoundException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Response;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        //
    })
    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->report(function (AccountAlreadExistsException $e) {
            abort(Response::HTTP_CONFLICT, $e->getMessage());
        });

        $exceptions->report(function (NotFoundException $e) {
            abort(Response::HTTP_NOT_FOUND, $e->getMessage());
        });

    })->create();
