<?php

namespace JessicaDigital\SocialFeed\Services;

use JessicaDigital\SocialFeed\Errors\ServiceError;
use JessicaDigital\SocialFeed\Items\FacebookItem;

class FacebookService extends BaseService {
	const API_URL = 'https://graph.facebook.com/v2.3/';
        
        private $accesstoken;
        protected $connection;
	protected $service = 'facebook';
        
        public function __construct($credentials) {
            $this->setCredentials($credentials);
        }

	public function setCredentials(array $credentials) {
            $this->requireCredentialKeys(['app_id', 'app_secret'], $credentials);
            $this->credentials = (object) $credentials;
	}
        
        private function getAccessToken() {
            $request = @file_get_contents(self::API_URL.'oauth/access_token?client_id='.$this->credentials->app_id.'&client_secret='.$this->credentials->app_secret.'&grant_type=client_credentials');
            if (empty($request)) {
                throw new ServiceError('Unable to fetch access token.');
            }
            $data = json_decode($request);
            if (empty($data->access_token)) {
                throw new ServiceError('No access token available.');
            }
            $this->accesstoken = $data->access_token;
        }

	public function getFeed($username) {
            $user = $this->getGraph($username);
            return $this->getFeedById($user->id);
	}
        
        public function getFeedById($id) {
            $response = [];
            $data = $this->getGraph($id.'/posts', 'fields=picture,message,from,created_time,likes.limit(1).summary(true),shares,comments.limit(1).summary(true)&limit=100');
            foreach ($data->data as $item) {
                $response[] = new FacebookItem($item);
            }
            return $response;
        }

	public function getItem($id) {
            return $this->parseItem($this->getGraph($id));
	}
        
        public function getIdFromUrl($url) {
            if (preg_match('/\/posts\/([0-9]+)/i', $url, $matches)) {
                return $matches[1];
            }
        }

	protected function getGraph($endpoint, $attributes = '') {
            if (empty($this->accesstoken)) {
                $this->getAccessToken();
            }
            $url = self::API_URL.$endpoint.'?access_token='.$this->accesstoken.'&'.$attributes;
            $request = json_decode(@file_get_contents($url));
            if (empty($request)) {
                throw new ServiceError('Could not load feed, check access token for request '.$url);
            }
            return $request;
	}

}