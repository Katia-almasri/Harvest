<?php

use App\Enums\General\StatusCodeEnum;
use App\Helpers\ResponseHelper;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Support\Facades\Route;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\TooManyRequestsHttpException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
        then: function () {
            Route::middleware('api')->namespace('Admin')->prefix('admin')->name('admin')->group(base_path('routes/admin.php'));
            Route::middleware('api')->namespace('Customer')->prefix('customer')->name('customer')->group(base_path('routes/customer.php'));
        },
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
            'role' => \Spatie\Permission\Middleware\RoleMiddleware::class,
            'permission' => \Spatie\Permission\Middleware\PermissionMiddleware::class,
            'role_or_permission' => \Spatie\Permission\Middleware\RoleOrPermissionMiddleware::class,
        ]);
        $middleware->throttleApi('60,1');


    })
    ->withExceptions(function (Exceptions $exceptions) {
        $responseHelper = new ResponseHelper();
        //1. not found exception
        $exceptions->renderable(function (NotFoundHttpException $e) use ($responseHelper) {
            return $responseHelper->apiResponse(null, StatusCodeEnum::STATUS_NOT_FOUND, 'Not Found');
        });

        //2. too many requests
        $exceptions->renderable(function (TooManyRequestsHttpException $e) use ($responseHelper) {
            return $responseHelper->apiResponse(null, StatusCodeEnum::TOO_MANY_REQUESTS, 'Too Many Requests');
        });



    })->create();
