<?php

namespace Dadata;

class DadataClient
{
    private $cleaner;
    private $profile;
    private $suggestions;

    public function __construct($token, $secret)
    {
        $this->cleaner = new CleanClient($token, $secret);
        $this->profile = new ProfileClient($token, $secret);
        $this->suggestions = new SuggestClient($token);
    }

    public function clean($name, $value)
    {
        return $this->cleaner->clean($name, $value);
    }

    public function cleanRecord($structure, $record)
    {
        return $this->cleaner->cleanRecord($structure, $record);
    }

    public function findAffiliated($query, $count = Settings::SUGGESTION_COUNT, $kwargs = [])
    {
        return $this->suggestions->findAffiliated($query, $count, $kwargs);
    }

    public function findById($name, $query, $count = Settings::SUGGESTION_COUNT, $kwargs = [])
    {
        return $this->suggestions->findById($name, $query, $count, $kwargs);
    }

    public function geolocate($name, $lat, $lon, $radiusMeters = 100, $count = Settings::SUGGESTION_COUNT, $kwargs = [])
    {
        return $this->suggestions->geolocate($name, $lat, $lon, $radiusMeters, $count, $kwargs);
    }

    public function getBalance()
    {
        return $this->profile->getBalance();
    }

    public function getDailyStats($date = null)
    {
        return $this->profile->getDailyStats($date);
    }

    public function getVersions()
    {
        return $this->profile->getVersions();
    }

    public function iplocate($ip, $kwargs = [])
    {
        return $this->suggestions->iplocate($ip, $kwargs);
    }

    public function suggest($name, $query, $count = Settings::SUGGESTION_COUNT, $kwargs = [])
    {
        return $this->suggestions->suggest($name, $query, $count, $kwargs);
    }
}
