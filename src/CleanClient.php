<?php

namespace Dadata;

use GuzzleHttp\Exception\GuzzleException;

class CleanClient extends ClientBase
{
    const BASE_URL = "https://cleaner.dadata.ru/api/v1/";

    public function __construct($token, $secret)
    {
        parent::__construct(self::BASE_URL, $token, $secret);
    }

    /**
     * @throws GuzzleException
     */
    public function clean($name, $value)
    {
        $url = "clean/$name";
        $fields = array($value);
        $response = $this->post($url, $fields);
        return $response[0];
    }

    /**
     * @throws GuzzleException
     */
    public function cleanRecord($structure, $record)
    {
        $url = "clean";
        $data = [
            "structure" => $structure,
            "data" => [$record]
        ];
        $response = $this->post($url, $data);
        return $response["data"][0];
    }
}
