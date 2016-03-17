<?php

namespace JessicaDigital\SocialFeed\Services;

use Abraham\TwitterOAuth\TwitterOAuth;
use JessicaDigital\SocialFeed\Errors;
use JessicaDigital\SocialFeed\Items;

class TwitterService extends BaseService {
    protected $connection;

    public function __construct($credentials = array()) {
        $this->setCredentials($credentials);
    }
        
    protected function getConnection() {
        if (!empty($this->connection)) {
            return $this->connection;
        }
        try {
            return $this->connection = new TwitterOAuth($this->credentials->consumer_key, $this->credentials->consumer_secret, $this->credentials->access_token, $this->credentials->access_token_secret);
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
            $response[] = $this->parseItem($item);
        }
        return $response;
    }

    public function getItem($id) {
        return $this->parseItem($this->getConnection()->get('statuses/show/'.$id));
    }

    public function getIdFromUrl($url) {
        if (preg_match('/status\/([0-9]+)/i', $url, $matches)) {
            return $matches[1];
        }
        return null;
    }

    protected function parseItem($item) {
        $response = new Items\Item();
        $user = new Items\User();
        $media = new Items\Media();
        $response->service = $this->service;
        $response->id = $item->id;
        $response->created = strtotime($item->created_at);
        if (!empty($item->retweeted_status->user->id)) {
            $user->id = $item->retweeted_status->user->id;
            $user->handle = $item->retweeted_status->user->screen_name;
            $user->image = $item->retweeted_status->user->profile_image_url_https;
            $user->link = $item->retweeted_status->user->url;
            $user->name = $item->retweeted_status->user->name;
        } else {
            $user->id = $item->user->id;
            $user->handle = $item->user->screen_name;
            $user->image = $item->user->profile_image_url_https;
            $user->link = $item->user->url;
            $user->name = $item->user->name;
        }
        $response->link = 'https://twitter.com/'.$user->handle.'/status/'.$response->id;
        $response->text = $item->text;

        if (isset($item->extended_entities->media)) {
            $img = $item->extended_entities->media[0];
            $media->image = $img->media_url_https;
        }

        if (isset($item->entities->urls)) {
            foreach ($item->entities->urls as $url) {
                $parsed = $this->mediaFromUrl($url->expanded_url);
                if (!empty($parsed->image) || !empty($parsed->video->id))
                    $media = $parsed;
                break;
            }
        }

        $response->user = $user;
        $response->media = $media;

        return $this->process($response);
    }
}