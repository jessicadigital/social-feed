<?php

namespace JessicaDigital\SocialFeed\Services;

use JessicaDigital\SocialFeed\Errors\ServiceError;
use JessicaDigital\SocialFeed\Items\FacebookItem;

class PinterestService extends BaseService {
	const API_URL = 'https://api.pinterest.com/v3/';
        
        protected $connection;
	protected $service = 'pinterest';
        
        public function __construct() {
        }

	public function setCredentials(array $credentials) {
            $this->requireCredentialKeys([], $credentials);
            $this->credentials = (object) $credentials;
	}

	public function getFeed($username) {
            $feeditems = $this->getAPI('pidgets/users/'.$username.'/pins/');
            $items = array();
            
            if (!empty($feeditems->data->pins)) {
                foreach ($feeditems->data->pins as $feeditem) {
                    $items[] = new \JessicaDigital\SocialFeed\Items\PinterestItem($feeditem);
                }
            }
            return $items;
	}

	public function getItem($id) {
            return false;
	}
        
        public function getIdFromUrl($url) {
            if (preg_match('/\/posts\/([0-9]+)/i', $url, $matches)) {
                return $matches[1];
            }
        }

	protected function getAPI($endpoint, $attributes = '') {
            
            $url = self::API_URL.$endpoint.'?'.$attributes;
            $request = json_decode(@file_get_contents($url));
            if (empty($request)) {
                throw new ServiceError('Could not load feed for request '.$url);
            }
            return $request;
	}

}