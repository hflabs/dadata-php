<?php

namespace Dadata;

use DateTime;
use GuzzleHttp\Exception\GuzzleException;

/**
 * Dadata API Client with request caching support
 *
 * Extends the standard DadataClient by adding the ability to cache
 * API request results to save resources and improve performance
 */
class CacheableDadataClient extends DadataClient
{
    private $cache;

    /** @var int Cache lifetime in seconds (default: 1 hour) */
    private $ttl = 3600;

    /** @var bool Flag to enable/disable caching */
    private $cacheEnabled = true;

    /** @var array Cache usage statistics */
    private $cacheStats = array(
        'hits'   => 0,    // Number of successful cache hits
        'misses' => 0,    // Number of cache misses (data not in cache)
        'writes' => 0,    // Number of cache writes
    );

    /** @var array Methods for which caching is enabled */
    private $cacheMethods = array(
        'clean'          => true,
        'suggest'        => true,
        'findById'       => true,
        'geolocate'      => true,
        'iplocate'       => true,
        'findAffiliated' => true,
    );

    public function __construct($token, $secret, $cache, array $options = array())
    {
        parent::__construct($token, $secret);
        $this->cache = $cache;

        // Apply caching options if specified
        if (isset($options['ttl'])) {
            $this->ttl = (int)$options['ttl'];
        }

        if (isset($options['enabled'])) {
            $this->cacheEnabled = (bool)$options['enabled'];
        }

        if (isset($options['methods']) && is_array($options['methods'])) {
            $this->cacheMethods = array_merge($this->cacheMethods, $options['methods']);
        }
    }

    /**
     * Get current cache configuration
     *
     * @return array Array with cache settings
     */
    public function getCacheConfig()
    {
        return array(
            'enabled' => $this->cacheEnabled,
            'ttl'     => $this->ttl,
            'methods' => $this->cacheMethods,
            'stats'   => $this->cacheStats
        );
    }

    /**
     * Enable caching
     *
     * @return $this
     */
    public function enableCache()
    {
        $this->cacheEnabled = true;
        return $this;
    }

    /**
     * Disable caching
     *
     * @return $this
     */
    public function disableCache()
    {
        $this->cacheEnabled = false;
        return $this;
    }

    /**
     * Set cache lifetime
     *
     * @param int $seconds Time in seconds
     * @return $this
     */
    public function setCacheTtl($seconds)
    {
        $this->ttl = (int)$seconds;
        return $this;
    }

    /**
     * Enable caching for specified method
     *
     * @param string $method Method name
     * @return $this
     */
    public function enableCacheForMethod($method)
    {
        $this->cacheMethods[$method] = true;
        return $this;
    }

    /**
     * Disable caching for specified method
     *
     * @param string $method Method name
     * @return $this
     */
    public function disableCacheForMethod($method)
    {
        $this->cacheMethods[$method] = false;
        return $this;
    }

    /**
     * Get cache usage statistics
     *
     * @return array Array with statistics
     */
    public function getCacheStats()
    {
        return $this->cacheStats;
    }

    /**
     * Reset cache usage statistics
     *
     * @return $this
     */
    public function resetCacheStats()
    {
        $this->cacheStats = array(
            'hits'   => 0,
            'misses' => 0,
            'writes' => 0,
        );
        return $this;
    }

    /**
     * Clean data method with caching support
     *
     * @param string $name Data type
     * @param string $value Value to clean
     * @return array Cleaning result
     */
    public function clean($name, $value)
    {
        return $this->cacheMethodCall('clean', function ($name, $value) {
            return parent::clean($name, $value);
        }, array($name, $value));
    }

    /**
     * Wraps method call with caching logic
     *
     * @param string $method Method name
     * @param callable $callback Function that performs API request
     * @param array $args Method arguments
     * @return mixed Method execution result
     */
    private function cacheMethodCall($method, $callback, array $args)
    {
        // Check if cache should be used
        if (!$this->shouldCache($method)) {
            return call_user_func_array($callback, $args);
        }

        // Create cache key
        $cacheArgs = array_merge(array($method), $args);
        $cacheKey = call_user_func_array(array($this, 'buildCacheKey'), $cacheArgs);

        // Check if data exists in cache
        if ($this->cache->has($cacheKey)) {
            $this->cacheStats['hits']++;
            return $this->cache->get($cacheKey);
        }

        // No data in cache, perform request
        $this->cacheStats['misses']++;
        $result = call_user_func_array($callback, $args);

        // Save result to cache
        $this->cache->set($cacheKey, $result, $this->ttl);
        $this->cacheStats['writes']++;

        return $result;
    }

    /**
     * Checks if results for specified method should be cached
     *
     * @param string $method Method name
     * @return bool true if caching is enabled for method
     */
    private function shouldCache($method)
    {
        return $this->cacheEnabled &&
            isset($this->cacheMethods[$method]) &&
            $this->cacheMethods[$method] === true;
    }

    /**
     * Suggest method with caching support
     *
     * @param string $name Data type
     * @param string $query Query text
     * @param int $count Number of results
     * @param array $kwargs Additional parameters
     * @return array Search results
     */
    public function suggest($name, $query, $count = Settings::SUGGESTION_COUNT, $kwargs = array())
    {
        return $this->cacheMethodCall('suggest', function ($name, $query, $count, $kwargs) {
            return parent::suggest($name, $query, $count, $kwargs);
        }, array($name, $query, $count, $kwargs));
    }

    /**
     * Find by ID method with caching support
     *
     * @param string $name Data type
     * @param string $query Identifier
     * @param int $count Number of results
     * @param array $kwargs Additional parameters
     * @return array Search results
     */
    public function findById($name, $query, $count = Settings::SUGGESTION_COUNT, $kwargs = array())
    {
        return $this->cacheMethodCall('findById', function ($name, $query, $count, $kwargs) {
            return parent::findById($name, $query, $count, $kwargs);
        }, array($name, $query, $count, $kwargs));
    }

    /**
     * Geolocate method with caching support
     *
     * @param string $name Data type
     * @param float $lat Latitude
     * @param float $lon Longitude
     * @param int $radiusMeters Search radius in meters
     * @param int $count Number of results
     * @param array $kwargs Additional parameters
     * @return array Search results
     */
    public function geolocate($name, $lat, $lon, $radiusMeters = 100, $count = Settings::SUGGESTION_COUNT, $kwargs = array())
    {
        return $this->cacheMethodCall('geolocate', function ($name, $lat, $lon, $radiusMeters, $count, $kwargs) {
            return parent::geolocate($name, $lat, $lon, $radiusMeters, $count, $kwargs);
        }, array($name, $lat, $lon, $radiusMeters, $count, $kwargs));
    }

    /**
     * IP locate method with caching support
     *
     * @param string $ip IP address
     * @param array $kwargs Additional parameters
     * @return array Location information
     */
    public function iplocate($ip, $kwargs = array())
    {
        return $this->cacheMethodCall('iplocate', function ($ip, $kwargs) {
            return parent::iplocate($ip, $kwargs);
        }, array($ip, $kwargs));
    }

    /**
     * Find affiliated companies method with caching support
     *
     * @param string $query Company INN or individual INN/SNILS
     * @param int $count Number of results
     * @param array $kwargs Additional parameters
     * @return array Search results
     */
    public function findAffiliated($query, $count = Settings::SUGGESTION_COUNT, $kwargs = array())
    {
        return $this->cacheMethodCall('findAffiliated', function ($query, $count, $kwargs) {
            return parent::findAffiliated($query, $count, $kwargs);
        }, array($query, $count, $kwargs));
    }

    /**
     * Get user balance without caching
     *
     * @return float User balance
     * @throws GuzzleException
     */
    public function getBalance()
    {
        // No caching for this method as data may change frequently
        return parent::getBalance();
    }

    /**
     * Get daily statistics without caching
     *
     * @param DateTime|null $date Statistics date
     * @return array Usage statistics
     * @throws GuzzleException
     */
    public function getDailyStats($date = null)
    {
        // No caching for this method as data may change frequently
        return parent::getDailyStats($date);
    }

    /**
     * Get API versions without caching
     *
     * @return array Version information
     * @throws GuzzleException
     */
    public function getVersions()
    {
        // No caching for this method as data may change
        return parent::getVersions();
    }

    /**
     * Build cache key
     *
     * @return string Unique cache key
     */
    private function buildCacheKey()
    {
        $args = func_get_args();
        $method = array_shift($args);
        return 'dadata_' . $method . '_' . md5(serialize($args));
    }
}