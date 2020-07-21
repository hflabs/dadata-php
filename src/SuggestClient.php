<?php

namespace Dadata;

class SuggestClient extends ClientBase
{
    const BASE_URL = "https://suggestions.dadata.ru/suggestions/api/4_1/rs/";

    public function __construct($token)
    {
        parent::__construct(self::BASE_URL, $token, null);
    }

    public function findAffiliated($query, $count = Settings::SUGGESTION_COUNT, $kwargs = [])
    {
        $url = "findAffiliated/party";
        $data = ["query" => $query, "count" => $count];
        $data = $data + $kwargs;
        $response = $this->post($url, $data);
        return $response["suggestions"];
    }

    public function findById($name, $query, $count = Settings::SUGGESTION_COUNT, $kwargs = [])
    {
        $url = "findById/$name";
        $data = ["query" => $query, "count" => $count];
        $data = $data + $kwargs;
        $response = $this->post($url, $data);
        return $response["suggestions"];
    }

    public function geolocate($name, $lat, $lon, $radiusMeters = 100, $count = Settings::SUGGESTION_COUNT, $kwargs = [])
    {
        $url = "geolocate/$name";
        $data = array(
            "lat" => $lat,
            "lon" => $lon,
            "radius_meters" => $radiusMeters,
            "count" => $count,
        );
        $data = $data + $kwargs;
        $response = $this->post($url, $data);
        return $response["suggestions"];
    }

    public function iplocate($ip, $kwargs = [])
    {
        $url = "iplocate/address";
        $query = ["ip" => $ip];
        $query = $query + $kwargs;
        $response = $this->get($url, $query);
        return $response["location"];
    }

    public function suggest($name, $query, $count = Settings::SUGGESTION_COUNT, $kwargs = [])
    {
        $url = "suggest/$name";
        $data = ["query" => $query, "count" => $count];
        $data = $data + $kwargs;
        $response = $this->post($url, $data);
        return $response["suggestions"];
    }
}
