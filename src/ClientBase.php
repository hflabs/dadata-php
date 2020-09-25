<?php

namespace Dadata;

abstract class ClientBase
{
    public $client;

    public $headers;

    public function __construct($baseUrl, $token, $secret = null)
    {
        $this->headers = [
            "Content-Type" => "application/json",
            "Accept" => "application/json",
            "Authorization" => "Token " . $token,
        ];
        if ($secret) {
            $this->headers["X-Secret"] = $secret;
        }
        $this->client = new \GuzzleHttp\Client([
            "base_uri" => $baseUrl,
            "headers" => $this->headers,
            "timeout" => Settings::TIMEOUT_SEC
        ]);
    }

    protected function get($url, $query = [])
    {
        $response = $this->client->get($url, ["query" => $query]);
        return json_decode($response->getBody(), true);
    }

    protected function post($url, $data)
    {
        $response = $this->client->post($url, [
            "json" => $data
        ]);
        return json_decode($response->getBody(), true);
    }
}
