<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        // Pastikan path ke file web.php benar
        web: __DIR__.'/../routes/web.php', 

        // INI BAGIAN YANG PALING PENTING UNTUK ANDA
        // Pastikan baris ini ada dan tidak di-comment
        api: __DIR__.'/../routes/api.php', 
        
        // Baris ini secara otomatis menambahkan awalan /api
        apiPrefix: 'api',

        // Konfigurasi lain (jika ada)
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        //
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
