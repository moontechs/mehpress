<?php

// Production-safe Laravel preload file
// Only preload core production classes, avoid dev dependencies

if (!file_exists(__DIR__ . '/../vendor/autoload.php')) {
    return;
}

require_once __DIR__ . '/../vendor/autoload.php';

// Define core Laravel classes to preload
$coreClasses = [
    // Foundation
    \Illuminate\Foundation\Application::class,
    \Illuminate\Foundation\Http\Kernel::class,
    \Illuminate\Foundation\Http\Middleware\ValidatePostSize::class,
    \Illuminate\Foundation\Http\Middleware\TrimStrings::class,

    // Container
    \Illuminate\Container\Container::class,

    // HTTP
    \Illuminate\Http\Request::class,
    \Illuminate\Http\Response::class,
    \Illuminate\Http\JsonResponse::class,

    // Routing
    \Illuminate\Routing\Router::class,
    \Illuminate\Routing\Route::class,
    \Illuminate\Routing\RouteCollection::class,

    // Support
    \Illuminate\Support\ServiceProvider::class,
    \Illuminate\Support\Facades\Facade::class,
    \Illuminate\Support\Collection::class,

    // Config
    \Illuminate\Config\Repository::class,

    // Database (if using Eloquent)
    \Illuminate\Database\Eloquent\Model::class,
    \Illuminate\Database\Query\Builder::class,
];

foreach ($coreClasses as $class) {
    if (class_exists($class, false)) {
        try {
            $reflection = new ReflectionClass($class);
            $filename = $reflection->getFileName();

            // Skip database seeders and other dev-only files
            if ($filename && is_readable($filename) &&
                !str_contains($filename, '/database/seeders/') &&
                !str_contains($filename, '/database/factories/') &&
                !str_contains($filename, '/tests/')) {
                opcache_compile_file($filename);
            }
        } catch (Exception $e) {
            // Skip classes that can't be reflected safely
        }
    }
}

// Preload app service providers (production only)
$providers = [
    App\Providers\AppServiceProvider::class,
];

foreach ($providers as $provider) {
    if (class_exists($provider, false)) {
        try {
            $reflection = new ReflectionClass($provider);
            $filename = $reflection->getFileName();
            if ($filename && is_readable($filename)) {
                opcache_compile_file($filename);
            }
        } catch (Exception $e) {
            // Skip providers that can't be loaded
        }
    }
}