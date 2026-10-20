<?php

namespace Dadata;

use GuzzleHttp\Client;

final class CleanTest extends BaseTestCase
{
    /**
     * @before
     */
    protected function setUpApi()
    {
        $this->setUpMock();
        $this->api = new CleanClient("token", "secret");
        $this->api->client = new Client(["handler" => $this->handler]);
    }

    public function testToken()
    {
        $api = new CleanClient("123", "456");
        $headers = $api->client->getConfig("headers");
        $this->assertEquals("Token 123", $headers["Authorization"]);
    }

    public function testSecret()
    {
        $api = new CleanClient("123", "456");
        $headers = $api->client->getConfig("headers");
        $this->assertEquals("456", $headers["X-Secret"]);
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
        $this->assertEquals($expected, $actual);
    }

    public function testCleanRequest()
    {
        $this->mockResponse([
            [ "source" => "москва"]
        ]);
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
        $this->assertEquals($expected, $actual);
    }
}
