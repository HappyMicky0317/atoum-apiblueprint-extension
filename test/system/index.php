<?php

require dirname(__DIR__, 2) . '/vendor/autoload.php';

$router = new Hoa\Router\Http();
$router
    ->get(
        'test1',
        '/test1',
        function () {
            header('Content-Type: text/plain;charset=UTF-8');

            echo 'Hello, World!';
        }
    )
    ->get(
        'schema',
        '/schema',
        function () {
            header('Content-Type: application/json');

            echo '{"message": "Hello, World!"}';
        }
    );

try {
    $dispatcher = new Hoa\Dispatcher\Basic();
    $dispatcher->dispatch($router);
} catch (\Exception $e) {
    header('HTTP/1.1 404 Not Found');
}
