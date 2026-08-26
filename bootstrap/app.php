<?php

use App\Http\Middleware\DemoAuthMiddleware;
use App\Http\Middleware\RoleMiddleware;
use App\Http\Middleware\UpdateUserLastSeen;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withSchedule(function (Schedule $schedule): void {
        $schedule->command('send:deadline-reminders')->dailyAt('08:00');
    })
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->web(append: [
            DemoAuthMiddleware::class,
            UpdateUserLastSeen::class,
        ]);
        // Daftarkan alias middleware role di sini
        $middleware->alias([
            'role' => RoleMiddleware::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
