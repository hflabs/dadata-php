<?php

namespace Dadata;

use DateTime;
use GuzzleHttp\Client;

final class ProfileTest extends BaseTest
{
    protected function setUp()
    {
        parent::setUp();
        $this->api = new ProfileClient("token", "secret");
        $this->api->client = new Client(["handler" => $this->handler]);
    }

    public function testToken()
    {
        $api = new ProfileClient("123", "456");
        $headers = $api->client->getConfig("headers");
        $this->assertEquals($headers["Authorization"], "Token 123");
    }

    public function testSecret()
    {
        $api = new ProfileClient("123", "456");
        $headers = $api->client->getConfig("headers");
        $this->assertEquals($headers["X-Secret"], "456");
    }

    public function testGetBalance()
    {
        $response = ["balance" => 9922.30];
        $this->mockResponse($response);
        $actual = $this->api->getBalance();
        $this->assertEquals($actual, 9922.30);
    }

    public function testGetDailyStats()
    {
        $today = new DateTime();
        $todayStr = $today->format("Y-m-d");
        $expected = ["date" => $todayStr, "services" => ["merging" => 0, "suggestions" => 11, "clean" => 1004]];
        $this->mockResponse($expected);
        $actual = $this->api->getDailyStats();
        $this->assertEquals($actual, $expected);
    }

    public function testVersions()
    {
        $expected = [
            "dadata" => ["version" => "17.1 (5995:3d7b54a78838)"],
            "suggestions" => ["version" => "16.10 (5a2e47f29553)", "resources" => ["ЕГРЮЛ" => "13.01.2017"]],
            "factor" => ["version" => "8.0 (90780)", "resources" => ["ФИАС" => "30.01.2017"]],
        ];
        $this->mockResponse($expected);
        $actual = $this->api->getVersions();
        $this->assertEquals($actual, $expected);
    }
}
