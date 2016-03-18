<?php

namespace JessicaDigital\SocialFeed\Services;

use JessicaDigital\SocialFeed\Errors;
use JessicaDigital\SocialFeed\Items;

class YoutubeService extends BaseService {
    protected $connection;
    protected $service = 'youtube';

    public function __construct($credentials = array()) {
        $this->setCredentials($credentials);
    }

    public function setCredentials(array $credentials) {
        $this->requireCredentialKeys(['api_key'], $credentials);
        $this->credentials = (object) $credentials;
    }
    
    private function getAPI($endpoint, $params = '') {
        $response = json_decode(@file_get_contents('https://www.googleapis.com/youtube/v3/'.$endpoint.'?key='.$this->credentials->api_key.'&'.$params));
        if (empty($response)) {
            throw new Errors\ServiceError('Unable to fetch feed.');
        }        
        return $response->items;
    }

    public function getFeed($channel_id) {
        $response = [];
        foreach ($this->getAPI('search','channelId='.$channel_id.'&part=snippet,contentDetails,id') as $item) {
            $response[] = new Items\YoutubeItem($item);
        }
        return $response;
    }

    public function getItem($id) {
        throw new Errors\ServiceError('This feature has not been implemented.');
    }

    public function getIdFromUrl($url) {
        throw new Errors\ServiceError('This feature has not been implemented.');
    }
}