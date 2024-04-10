<?php

namespace Dadata;

use GuzzleHttp\Exception\GuzzleException;

class DadataClient
{
    private $cleaner;
    private $profile;
    private $suggestions;

    public function __construct($token, $secret)
    {
        $this->cleaner = new CleanClient($token, $secret);
        $this->profile = new ProfileClient($token, $secret);
        $this->suggestions = new SuggestClient($token, $secret);
    }

    /**
     * @throws GuzzleException
     */
    public function clean($name, $value)
    {
        return $this->cleaner->clean($name, $value);
    }

    /**
     * @throws GuzzleException
     */
    public function cleanRecord($structure, $record)
    {
        return $this->cleaner->cleanRecord($structure, $record);
    }

    /**
     * @throws GuzzleException
     */
    public function findAffiliated($query, $count = Settings::SUGGESTION_COUNT, $kwargs = [])
    {
        return $this->suggestions->findAffiliated($query, $count, $kwargs);
    }

    /**
     * @throws GuzzleException
     */
    public function findById($name, $query, $count = Settings::SUGGESTION_COUNT, $kwargs = [])
    {
        return $this->suggestions->findById($name, $query, $count, $kwargs);
    }

    /**
     * @throws GuzzleException
     */
    public function geolocate($name, $lat, $lon, $radiusMeters = 100, $count = Settings::SUGGESTION_COUNT, $kwargs = [])
    {
        return $this->suggestions->geolocate($name, $lat, $lon, $radiusMeters, $count, $kwargs);
    }

    /**
     * @throws GuzzleException
     */
    public function getBalance()
    {
        return $this->profile->getBalance();
    }

    /**
     * @throws GuzzleException
     */
    public function getDailyStats($date = null)
    {
        return $this->profile->getDailyStats($date);
    }

    /**
     * @throws GuzzleException
     */
    public function getVersions()
    {
        return $this->profile->getVersions();
    }

    /**
     * @throws GuzzleException
     */
    public function iplocate($ip, $kwargs = [])
    {
        return $this->suggestions->iplocate($ip, $kwargs);
    }

    /**
     * @throws GuzzleException
     */
    public function suggest($name, $query, $count = Settings::SUGGESTION_COUNT, $kwargs = [])
    {
        return $this->suggestions->suggest($name, $query, $count, $kwargs);
    }
}
