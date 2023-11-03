<?php

use assignment\data\Url;
use Klein\Request;
use Klein\Response;

return function (Request $request, Response $response): void {
    if ($response->isSent() || $response->isLocked()) {
        return;
    }

    $urls = Url::findAll();

    $body = [];
    foreach ($urls as $url) {
        $body[] = $url->toDto();
    }

    $response->header("Content-Type", "application/json");
    $response->body(json_encode($body));
    $response->send();
};
