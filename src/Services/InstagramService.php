<?php

namespace JessicaDigital\SocialFeed\Services;

use JessicaDigital\SocialFeed\Items\InstagramItem;
use JessicaDigital\SocialFeed\Errors\ServiceError;

class InstagramService extends BaseService {
    const API_URL = 'https://api.instagram.com/v1/';

    protected $connection;
    protected $service = 'instagram';

    public function __construct($credentials = array()) {
        $this->setCredentials($credentials);
    }

    public function setCredentials(array $credentials) {
        $this->requireCredentialKeys(['access_token', 'client_id', 'client_secret', 'user_id'], $credentials);
        $this->credentials = (object) $credentials;
    }
    
    protected function getApi($endpoint) {
        $request = @file_get_contents(self::API_URL.$endpoint.(strpos($endpoint, '?') > -1 ? '&' : '?')."client_id={$this->credentials->client_id}&access_token={$this->credentials->access_token}");
        if ($request === false) {
            throw $this->serviceError("Could not load endpoint '$endpoint' from service {$this->service}, check credentials");
        }
        return json_decode($request);
    }

    public function getFeed($username) {
        $response = [];
        $data = $this->getApi("users/search?q=$username");
        $id = $data->data[0]->id;
        $data = $this->getApi("users/$id/media/recent/");
        foreach ($data->data as $item) {
            $response[] = new InstagramItem($item);
        }
        return json_decode($response);
    }
    
    public function getFeedById($userid) {
        $response = array();
        foreach ($this->getApi('users/'.$userid.'/media/recent/')->data as $item) {
            $response[] = new InstagramItem($item);
        }
        return $response;
    }

    public function getItem($id) {
        $url = urlencode('https://instagram.com/p/$id/');
        $data = $this->getApi('oembed?url='.$url);
        return new InstagramItem($this->getApi('media/'.$data->media_id)->data);
    }

    public function getIdFromUrl($url) {
        if (preg_match('/instagram\.com\/p\/([a-z0-9-_]+)\//i', $url, $matches)) {
            return $matches[1];
        }
        return null;
    }

}