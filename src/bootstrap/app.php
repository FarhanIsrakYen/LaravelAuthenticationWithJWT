<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Validation\UnauthorizedException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
use Symfony\Component\Routing\Exception\RouteNotFoundException;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . "/../routes/web.php",
        api: __DIR__ . "/../routes/api.php",
        commands: __DIR__ . "/../routes/console.php",
        health: "/up"
    )
    ->withMiddleware(function (Middleware $middleware) {
        //
    })
    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->render(function (Exception $exception, Request $request) {
            if ($exception instanceof TokenExpiredException) {
                return response()->json([
                    'success' => false,
                    'message' => 'Session expired. Please login again!'
                ],401);
            }

            if ($exception instanceof TokenInvalidException) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid token. Please login again!'
                ],401);
            }

            if ($exception instanceof JWTException) {
                return response()->json([
                    'success' => false,
                    'message' => 'Token is not provided'
                ],401);
            }

            if ($exception instanceof UnauthorizedHttpException) {
                return response()->json([
                    'success' => false,
                    'message' => 'Wrong username or password provided'
                ],401);
            }

            if ($exception instanceof RouteNotFoundException) {
                return response()->json([
                    'success' => false,
                    'message' => 'Wrong username or password provided'
                ],401);
            }
            return $request;
        });
    })
    ->create();
