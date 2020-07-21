<?php

namespace Dadata;

use GuzzleHttp\Client;

final class CleanTest extends BaseTest
{
    protected function setUp()
    {
        parent::setUp();
        $this->api = new CleanClient("token", "secret");
        $this->api->client = new Client(["handler" => $this->handler]);
    }

    public function testToken()
    {
        $api = new CleanClient("123", "456");
        $headers = $api->client->getConfig("headers");
        $this->assertEquals($headers["Authorization"], "Token 123");
    }

    public function testSecret()
    {
        $api = new CleanClient("123", "456");
        $headers = $api->client->getConfig("headers");
        $this->assertEquals($headers["X-Secret"], "456");
    }

    public function testClean()
    {
        $expected = [
            "source" => "Сережа",
            "result" => "Сергей",
            "qc" => 1
        ];
        $this->mockResponse([$expected]);
        $actual = $this->api->clean("name", "Сережа");
        $this->assertEquals($actual, $expected);
    }

    public function testCleanRequest()
    {
        $this->mockResponse([]);
        $this->api->clean("address", "москва");
        $expected = ["москва"];
        $actual = $this->getLastRequest();
        $this->assertEquals($expected, $actual);
    }

    public function testCleanRecord()
    {
        $structure = ["AS_IS", "AS_IS", "AS_IS"];
        $record = ["1", "2", "3"];
        $expected = [
            [
                "source" => "1",
            ],
            [
                "source" => "2",
            ],
            [
                "source" => "3",
            ]
        ];
        $response = [
            "structure" => $structure,
            "data" => [$expected]
        ];
        $this->mockResponse($response);
        $actual = $this->api->cleanRecord($structure, $record);
        $this->assertEquals($actual, $expected);
    }
}
