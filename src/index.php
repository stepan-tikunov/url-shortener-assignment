<?php

require __DIR__ . "/../vendor/autoload.php";

use Klein\Klein;
use Klein\Request;
use Klein\Response;

$shortenEndpointHandler = include("endpoints/shorten.php");
$redirectEndpointHandler = include("endpoints/redirect.php");
$urlsEndpointHandler = include("endpoints/urls.php");
$urlClicksEndpointHandler = include("endpoints/urlClicks.php");

$router = new Klein();

$router->respond(function($request, $response) {
    $response->header("Access-Control-Allow-Origin", "*");
    $response->header("Access-Control-Allow-Methods", "GET, POST");
    $response->header("Access-Control-Allow-Headers", "X-Requested-With");
});

$router->respond("GET", "/api/shorten", $shortenEndpointHandler);

$router->respond("GET", "/api/urls", $urlsEndpointHandler);

$router->respond("GET", "/api/urls/[:url]", $urlClicksEndpointHandler);

$router->respond("GET", "/", function (Request $request, Response $response) {
    if ($response->isSent() || $response->isLocked()) {
        return;
    }

    $body = file_get_contents(__DIR__ . "/../static/index.html");
    $response->body($body);
    $response->send();
});

$router->respond("GET", "/urls", function (Request $request, Response $response) {
    if ($response->isSent() || $response->isLocked()) {
        return;
    }

    $body = file_get_contents(__DIR__ . "/../static/urls.html");
    $response->body($body);
    $response->send();
});

$router->respond("GET", "/urls/[:url]", function (Request $request, Response $response) {
    if ($response->isSent() || $response->isLocked()) {
        return;
    }

    $body = file_get_contents(__DIR__ . "/../static/url-details.html");
    $response->body($body);
    $response->send();
});

$router->respond("GET", "/assets/css/[:path]", function (Request $request, Response $response) {
    if ($response->isSent() || $response->isLocked()) {
        return;
    }

    $path = $request->param("path");

    $body = file_get_contents(__DIR__ . "/../static/assets/css/" . $path);

    if (!$body) {
        $response->code(404);
        $response->body("Not Found");
        $response->send();
        return;
    }

    $response->body($body);
    $response->header("Content-Type", "text/css");
    $response->send();
});

$router->respond("GET", "/assets/js/[:path]", function (Request $request, Response $response) {
    if ($response->isSent() || $response->isLocked()) {
        return;
    }

    $path = $request->param("path");

    $body = file_get_contents(__DIR__ . "/../static/assets/js/" . $path);

    if (!$body) {
        $response->code(404);
        $response->body("Not Found");
        $response->send();
        return;
    }

    $response->body($body);
    $response->header("Content-Type", "text/javascript");
    $response->send();
});

$router->respond("GET", "/[:shortUrl]", $redirectEndpointHandler);



$router->dispatch();
