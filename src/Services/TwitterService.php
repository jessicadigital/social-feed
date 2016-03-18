<?php

namespace JessicaDigital\SocialFeed\Services;

use Abraham\TwitterOAuth\TwitterOAuth;
use JessicaDigital\SocialFeed\Errors;
use JessicaDigital\SocialFeed\Items;

class TwitterService extends BaseService {
    protected $connection;
    protected $service = 'twitter';

    public function __construct($credentials = array()) {
        $this->setCredentials($credentials);
    }
        
    protected function getConnection() {
        if (!empty($this->connection)) {
            return $this->connection;
        }
        try {
            return $this->connection = new TwitterOAuth(
                $this->credentials->consumer_key, 
                $this->credentials->consumer_secret, 
                $this->credentials->access_token, 
                $this->credentials->access_token_secret
            );
        }
        catch (Abraham\TwitterOAuth\TwitterOAuthException $e) {
            throw new Errors\ServiceError($e->getMessage());
        }
    }

    public function setCredentials(array $credentials) {
        $this->requireCredentialKeys(['consumer_key', 'consumer_secret', 'access_token', 'access_token_secret'], $credentials);
        $this->credentials = (object) $credentials;
    }

    public function getFeed($username) {
        $response = [];
        $connection = $this->getConnection();
        try {
            $data = $connection->get('statuses/user_timeline', ['screen_name' => $username]);
        }
        catch (\Abraham\TwitterOAuth\TwitterOAuthException $e) {
            throw new Errors\ServiceError($e->getMessage());
        }
        if (isset($data->errors)) {
            throw new Errors\ServiceError($data->errors[0]->message);
        }
        foreach ($data as $item) {
            $response[] = new Items\TwitterItem($item);
        }
        return $response;
    }

    public function getItem($id) {
        return new Items\TwitterItem($this->getConnection()->get('statuses/show/'.$id));
    }

    public function getIdFromUrl($url) {
        if (preg_match('/status\/([0-9]+)/i', $url, $matches)) {
            return $matches[1];
        }
        return null;
    }
}