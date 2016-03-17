<?php

namespace JessicaDigital\SocialFeed\Services;

class FacebookService extends BaseService {
	const API_URL = 'https://graph.facebook.com/v2.3/';

	protected $service = 'facebook';
	protected $connection;

	public function setCredentials(array $credentials) {
		$this->requireCredentialKeys(['app_id', 'app_secret'], $credentials);
		$this->credentials = (object) $credentials;
	}

	public function getFeed($username) {
		$response = [];
		$user = $this->getGraph("$username");
		$id = $user->id;
		$data = $this->getGraph("$username/feed");
		foreach ($data->data as $item) {
			$item = $this->parseItem($item, $id);
			if ($item !== null)
				$response[] = $item;
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
		$request = @file_get_contents("https://graph.facebook.com/?ids=".urlencode($url));
		if ($request === false)
			return null;
		$data = json_decode($request);
		return $data->{$url}->id;
	}

	protected function getGraph($endpoint) {
		$credentials = $this->getCredentials();
		$request = @file_get_contents(self::API_URL.$endpoint."?access_token={$credentials->app_id}|{$credentials->app_secret}");
		if ($request === false) {
			throw $this->serviceError('Could not load feed, check credentials');
		}
		return json_decode($request);
	}

	private function parseItem($item, $id = null) {
		if ($id !== null && $item->from->id != $id)
			return null;

		$response = new Item();
		$user = new User();
		$media = new Media();
		$response->service = $this->service;
		$response->id = $item->id;
		$response->created = strtotime($item->created_time);
		$user->id = $item->from->id;
		$user->name = $item->from->name;
		$user->image = "https://graph.facebook.com/v2.3/{$user->id}/picture/";
		$user->link = "https://facebook.com/profile.php?id={$user->id}";
		$response->link = $item->link;//"http://www.facebook.com/permalink.php?id={$user->id}&v=wall&story_fbid={$response->id}";
		if (isset($item->message))
			$response->text = $item->message;
		if (isset($item->type)) {
			switch ($item->type) {
				case 'photo':
					$media->image = "https://graph.facebook.com/{$item->object_id}/picture?type=normal";
					break;
				case 'video':
					$media = $this->mediaFromUrl($item->link);
			}
		}

		if (isset($item->images)) {
			$total = count($item->images);
			if ($total > 1)
				$media->image = $item->images[1]->source;
			else if ($total == 1)
				$media->image = $item->images[0]->source;
		}

		$response->user = $user;
		$response->media = $media;
		return $this->process($response);
	}

}