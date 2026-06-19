<?php

require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

$routes = $app->make('router')->getRoutes();

$apiRoutes = [];
foreach ($routes as $route) {
    if (str_starts_with($route->uri(), 'api/')) {
        $methods = array_diff($route->methods(), ['HEAD']);
        foreach ($methods as $method) {
            $apiRoutes[] = $method . ' /' . $route->uri();
        }
    }
}

sort($apiRoutes);
file_put_contents('routes_from_app.txt', implode(PHP_EOL, $apiRoutes));

$mdContent = file_get_contents('../dokumentasi/dokumentasi_api.md');
$missing = [];

foreach ($apiRoutes as $route) {
    if (strpos($mdContent, $route) === false) {
        $missing[] = $route;
    }
}

echo "Missing routes in dokumentasi_api.md:\n";
echo implode(PHP_EOL, $missing);
