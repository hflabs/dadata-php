<?php

namespace Dadata;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;

abstract class ClientBase
{
    public $client;

    public function __construct($baseUrl, $token, $secret = null)
    {
        $headers = [
            "Content-Type" => "application/json",
            "Accept" => "application/json",
            "Authorization" => "Token " . $token,
        ];
        if ($secret) {
            $headers["X-Secret"] = $secret;
        }
        $this->client = new Client([
            "base_uri" => $baseUrl,
            "headers" => $headers,
            "timeout" => Settings::TIMEOUT_SEC
        ]);
    }

    /**
     * @throws GuzzleException
     */
    protected function get($url, $query = [])
    {
        $response = $this->client->get($url, ["query" => $query]);
        return json_decode($response->getBody(), true);
    }

    /**
     * @throws GuzzleException
     */
    protected function post($url, $data)
    {
        $response = $this->client->post($url, [
            "json" => $data
        ]);
        return json_decode($response->getBody(), true);
    }
}
