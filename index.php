<?php

require 'vendor/autoload.php';

$app = new \Slim\App();
// Define app routes
$app->get('/hello/{name}', function ($request, $response, $args) {
    return $response->write("Hello " . $args['name']);
});

// Run app
$app->run();