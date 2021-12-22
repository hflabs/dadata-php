<?php

namespace Dadata;

abstract class ClientBase
{
    public $client;

    public function __construct($baseUrl, $token, $secret = null, $proxy = '')
    {
        $headers = [
            "Content-Type" => "application/json",
            "Accept" => "application/json",
            "Authorization" => "Token " . $token,
        ];
        if ($secret) {
            $headers["X-Secret"] = $secret;
        }
        $options = [
            "base_uri" => $baseUrl,
            "headers" => $headers,
            "timeout" => Settings::TIMEOUT_SEC
        ];
        if ($proxy) {
            $options['proxy'] = $proxy;
        }
        $this->client = new \GuzzleHttp\Client($options);
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
