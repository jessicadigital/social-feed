<?php

namespace JessicaDigital\SocialFeed\Services;

use JessicaDigital\SocialFeed\Items;

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

    protected function mediaFromUrl($url) {
        $media = new Items\Media();
        $video = new Items\Video();
        switch (1) {
            case preg_match('/vine\.co\/v\/([a-z0-9]+)/i', $url, $matches):
                    $video->id = $matches[1];
                    $video->service = 'vine';
                    $vine = @file_get_contents("http://vine.co/v/{$video->id}");
                    if ($vine !== false) {
                            preg_match('/property="og:image" content="(.*?)"/', $vine, $images);
                            if (isset($images[1]) && $images[1] != '')
                                    $video->image = $images[1];
                    }
                    break;
            case preg_match('/(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?)\/|.*[?&]v=)|youtu\.be\/)([^"&?\/ ]{11})/i', $url, $matches):
                    $video->id = $matches[1];
                    $video->service = 'youtube';
                    $video->image = "http://img.youtube.com/vi/{$video->id}/hqdefault.jpg";
                    break;
            case preg_match('/https?:\/\/(?:www\.|player\.)?vimeo.com\/(?:channels\/(?:\w+\/)?|groups\/([^\/]*)\/videos\/|album\/(\d+)\/video\/|video\/|)(\d+)(?:$|\/|\?)/i', $url, $matches):
                    $video->id = $matches[3];
                    $video->service = 'vimeo';
                    break;
            case preg_match('/instagram\.com\/p\/([a-z0-9-_]+)\//i', $url, $matches):
                            $data = @file_get_contents('http://api.instagram.com/oembed?url='.urlencode($url));
                            if ($data !== false) {
                                    $info = json_decode($data);
                                    if (strpos($info->html, 'video') > -1) {
                                            $video->id = $matches[0];
                                            $video->service = 'instagram';
                    $video->image = isset($info->thumbnail_url) ? $info->thumbnail_url : null;
                } else {
                    $media->image = isset($info->thumbnail_url) ? $info->thumbnail_url : null;
                                    }
                            }
                            break;
                    case preg_match('/facebook\.com\/.+\/videos\/([0-9]+)\//i', $url, $matches):
                            $video->id = $matches[1];
                            $video->service = 'facebook';
                            $video->image = "https://graph.facebook.com/{$video->id}/picture?type=large";
                            break;
                    //case preg_match('/amp\.twimg\.com\/v\/([a-z0-9-]+)/i', $url, $matches):
        default:
            $video = null;
            break;
            }
    if ($video == null || $video->service == null || $video->id == null) $video = null;
            $media->video = $video;
            return $media;
    }

protected function process(Items\Item $item) {
    return $item;
}

    protected function requireCredentialKeys(array $keys, array $credentials) {
            foreach ($keys as $key)
                    if (!array_key_exists($key, $credentials))
                            throw $this->e("Missing credential $key");
    }

    protected function getCredentials() {
        if (!isset($this->credentials)) {
            throw new \JessicaDigital\SocialFeed\Errors\CredentialError($this->service, 'all');
        }
        return $this->credentials;
    }

    protected function serviceError($error) {
        return new \JessicaDigital\SocialFeed\Errors\ServiceError("Service {$this->service} reports error: $error");
    }

    protected function e($msg) {
        return new \Exception($msg);
    }
}
