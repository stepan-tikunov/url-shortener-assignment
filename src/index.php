<?php

require __DIR__ . "/../vendor/autoload.php";

use Klein\Klein;

$shortenEndpointHandler = include("endpoints/shorten.php");
$redirectEndpointHandler = include("endpoints/redirect.php");
$urlsEndpointHandler = include("endpoints/urls.php");
$urlClicksEndpointHandler = include("endpoints/urlClicks.php");

$router = new Klein();

$router->respond("GET", "/shorten", $shortenEndpointHandler);

$router->respond("GET", "/urls", $urlsEndpointHandler);

$router->respond("GET", "/urls/[:url]", $urlClicksEndpointHandler);

// If this regex doesn't match url, it is valid for redirect
$router->respond("GET", "!@/[a-zA-Z0-9]{7}", function ($request, $response) {
    $response->code(404);
    $response->body("Not Found");
    $response->send();
});

$router->respond("GET", "/[:shortUrl]", $redirectEndpointHandler);

$router->dispatch();
