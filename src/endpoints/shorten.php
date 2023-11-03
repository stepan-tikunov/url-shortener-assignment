<?php

namespace assignment\endpoints;

use assignment\data\Url;
use assignment\encode\FormatPreservingEncoder;
use Klein\Request;
use Klein\Response;

return function (Request $request, Response $response): void {
    $url = $request->param("url");
    $url = filter_var($url, FILTER_SANITIZE_URL);

    $response->header("Content-Type", "application/json");

    if (!filter_var($url, FILTER_VALIDATE_URL)) {
        $body = json_encode([
            "success" => false,
            "error" => "bad_url",
        ]);

        $response->code(400);   // Bad request
        $response->body($body);
        $response->send();

        return;
    }

    $url = new Url($url);
    $id = $url->save();

    $encoder = new FormatPreservingEncoder();
    $shortUrl = $encoder->encode($id);

    $body = json_encode([
        "shortUrl" => $shortUrl,
        "success" => true,
    ]);

    $response->body($body);
    $response->send();
};
