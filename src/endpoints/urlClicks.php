<?php

use assignment\data\Url;
use assignment\data\UrlClick;
use assignment\encode\FormatPreservingEncoder;
use Klein\Request;
use Klein\Response;

return function (Request $request, Response $response): void {
    $response->header("Content-Type", "application/json");

    if ($response->isSent() || $response->isLocked()) {
        return;
    }

    $shortUrl = $request->param("url");

    if ($shortUrl === null) {
        return;
    }

    $encoder = new FormatPreservingEncoder();
    $id = $encoder->decode($shortUrl);
    $url = Url::get($id);

    if ($url === null) {
        $response->body(json_encode([
            "error" => "No such URL found"
        ]));
        $response->send();
        return;
    }

    $clicks = UrlClick::findClicksFor($url);

    $body = $url->toDto();
    $body["clicks"] = [];
    foreach ($clicks as $click) {
        $body["clicks"][] = $click->toDto();
    }

    $response->body(json_encode($body));
    $response->send();
};
