<?php

namespace assignment\endpoints;

use assignment\data\Url;
use assignment\data\UrlClick;
use assignment\encode\FormatPreservingEncoder;
use Klein\Request;
use Klein\Response;

function notFound(Response $response): void {
    $response->code(404);
    $response->body("Not Found");
    $response->send();
}

return function (Request $request, Response $response): void {
    if ($response->isSent() || $response->isLocked()) {
        return;
    }

    $shortUrl = $request->param("shortUrl", null);

    if ($shortUrl === null) {
        notFound($response);
        return;
    }

    $encoder = new FormatPreservingEncoder();
    $id = $encoder->decode($shortUrl);

    $url = Url::get($id);

    if ($url === null) {
        notFound($response);
        return;
    }

    $now = new \DateTime();

    $ip = $request->server()->get("HTTP_X_REAL_IP");
    $ip ??= $request->server()->get("REMOTE_ADDR");

    $click = new UrlClick($now, $ip, $url);

    $click->save();

    $response->redirect($url->getUrl());
};