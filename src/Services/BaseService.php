<?php

namespace JessicaDigital\SocialFeed\Services;

use JessicaDigital\SocialFeed\Errors;
use JessicaDigital\SocialFeed\Items;
use JessicaDigital\SocialFeed\Media;

/**
 * Extend to provide a social medium
 * @package JessicaDigital\SocialFeed
 */
abstract class BaseService {
    /** @var object */
    protected $credentials;
    /** @var string */
    protected $service;
    /** @var Config */
    protected $config;

    public function __construct($credentials = array()) {
        $this->credentials = (object)$credentials;
    }

    /**
     * @param array $credentials
     * @return void
     * @throws \Exception
     */
    abstract public function setCredentials(array $credentials);

    /**
     * @param string $username
     * @return Item[]
     */
    abstract public function getFeed($username);

    /**
     * @param string $id
     * @return Item|null
     */
    abstract public function getItem($id);

    /**
     * @param string $url
     * @return string|null
     */
    abstract public function getIdFromUrl($url);

    /**
     * @param string $url
     * @return Item|null
     */
    public function getItemFromUrl($url) {
        return $this->getItem($this->getIdFromUrl($url));
    }
    
    public function linkify($text) {
        return $text;
    }

    protected function requireCredentialKeys(array $keys, array $credentials) {
        foreach ($keys as $key) {
            if (!array_key_exists($key, $credentials)) {
                throw new Errors\CredentialError($key);
            }
        }
    }

    protected function getCredentials() {
        if (!isset($this->credentials)) {
            throw new Errors\CredentialError($this->service, 'all');
        }
        return $this->credentials;
    }

    protected function serviceError($error) {
        return new Errors\ServiceError("Service {$this->service} reports error: $error");
    }
    
    public function writeFeed($feed, $file) {
        file_put_contents($file, $this->getFeed($feed));
    }
}
