<?php

namespace JessicaDigital\SocialFeed\Services;

class InstagramService extends BaseService {
	const API_URL = 'https://api.instagram.com/v1/';

	protected $service = 'instagram';
	protected $connection;

	public function setCredentials(array $credentials) {
		$this->requireCredentialKeys(['client_id', 'client_secret'], $credentials);
		$this->credentials = (object) $credentials;
	}

	public function getFeed($username) {
		$response = [];
		$data = $this->getApi("users/search?q=$username");
		$id = $data->data[0]->id;
		$data = $this->getApi("users/$id/media/recent/");
		foreach ($data->data as $item) {
			$response[] = $this->parseItem($item);
		}
		return $response;
	}

	public function getItem($id) {
		$url = urlencode("https://instagram.com/p/$id/");
		$data = $this->getApi("oembed?url=$url");
		$media_id = $data->media_id;
		return $this->parseItem($this->getApi("media/$media_id")->data);
	}

	public function getIdFromUrl($url) {
		if (preg_match('/instagram\.com\/p\/([a-z0-9-_]+)\//i', $url, $matches)) {
			return $matches[1];
		}
		return null;
	}

	protected function getApi($endpoint) {
		$credentials = $this->getCredentials();
		$request = @file_get_contents(self::API_URL.$endpoint.(strpos($endpoint, '?') > -1 ? '&' : '?')."client_id={$credentials->client_id}");
		if ($request === false) {
			throw $this->serviceError("Could not load endpoint '$endpoint' from service {$this->service}, check credentials");
		}
		return json_decode($request);
	}

	private function parseItem($item) {
		$response = new Item();
		$user = new User();
		$media = new Media();
		$response->service = $this->service;
		$response->id = $item->id;
		$response->created = (int) $item->created_time;
		$user->id = $item->user->id;
		$user->name = $item->user->full_name;
		$user->handle = $item->user->username;
		$user->image = $item->user->profile_picture;
		$user->link = "https://instagram.com/{$user->handle}";
		$response->link = $item->link;
		$response->text = $item->caption->text;

		switch ($item->type) {
			case 'video':
				$media->video = new Video();
				$media->video->id = $response->id;
				$media->video->image = $item->images->standard_resolution->url;
				$media->video->service = 'instagram';
				break;
			case 'image':
				$media->image = $item->images->standard_resolution->url;
				break;
		}

		$response->user = $user;
		$response->media = $media;
		return $this->process($response);
	}

}